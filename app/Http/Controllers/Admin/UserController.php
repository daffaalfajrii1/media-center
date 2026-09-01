<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AdminPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('roles')
            ->when($request->search, fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($request->filled('role'), fn ($q) => $q->role($request->role))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        return view('admin.users.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUser($request);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $this->syncUserAccess($user, $request);

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $pengguna): View
    {
        $this->authorizeManage($pengguna);

        $pengguna->load(['roles', 'permissions']);

        return view('admin.users.edit', array_merge($this->formData(), [
            'user' => $pengguna,
            'selectedPermissions' => $pengguna->getAllPermissions()->pluck('name')->all(),
            'selectedRole' => $pengguna->roles->first()?->name,
        ]));
    }

    public function update(Request $request, User $pengguna): RedirectResponse
    {
        $this->authorizeManage($pengguna);

        $validated = $this->validateUser($request, $pengguna);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $pengguna->update($data);

        if (! $pengguna->hasRole(User::ROLE_SUPER_ADMIN) || Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)) {
            $this->syncUserAccess($pengguna, $request);
        }

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna): RedirectResponse
    {
        if ($pengguna->id === Auth::id()) {
            return redirect()
                ->route('admin.pengguna.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if (! AdminPermissions::canManageUser(Auth::user(), $pengguna)) {
            abort(403);
        }

        if ($pengguna->hasRole(User::ROLE_SUPER_ADMIN)) {
            $superAdminCount = User::role(User::ROLE_SUPER_ADMIN)->count();
            if ($superAdminCount <= 1) {
                return redirect()
                    ->route('admin.pengguna.index')
                    ->with('error', 'Tidak dapat menghapus super admin terakhir.');
            }
        }

        $pengguna->delete();

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function formData(): array
    {
        $actor = Auth::user();

        return [
            'assignablePermissions' => AdminPermissions::assignableFor($actor),
            'permissionLabels' => AdminPermissions::LABELS,
            'roleTemplates' => AdminPermissions::ROLE_TEMPLATES,
            'canAssignSuperAdmin' => $actor->hasRole(User::ROLE_SUPER_ADMIN),
        ];
    }

    /** @return array<string, mixed> */
    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
            'role' => ['nullable', 'string', Rule::in(array_merge(array_keys(AdminPermissions::ROLE_TEMPLATES), [User::ROLE_SUPER_ADMIN, 'custom']))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ], [], [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'role' => 'role',
            'permissions' => 'hak akses',
        ]);
    }

    /** @return list<string> */
    private function resolvePermissions(Request $request): array
    {
        $actor = Auth::user();
        $requested = $request->input('permissions', []);

        if (! is_array($requested)) {
            $requested = [];
        }

        if (! in_array('view dashboard', $requested, true)) {
            $requested[] = 'view dashboard';
        }

        $permissions = AdminPermissions::sanitizeForActor($actor, $requested);

        if ($permissions === []) {
            $permissions = ['view dashboard'];
        }

        return $permissions;
    }

    private function resolveRole(Request $request, array $permissions): ?string
    {
        $role = $request->input('role');

        if ($role === 'custom' || empty($role)) {
            return null;
        }

        if ($role === User::ROLE_SUPER_ADMIN) {
            if (! Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)) {
                abort(403);
            }

            return User::ROLE_SUPER_ADMIN;
        }

        if (isset(AdminPermissions::ROLE_TEMPLATES[$role])) {
            $templatePerms = AdminPermissions::ROLE_TEMPLATES[$role]['permissions'];
            sort($templatePerms);
            $sorted = $permissions;
            sort($sorted);

            if ($templatePerms === $sorted) {
                return $role;
            }
        }

        return null;
    }

    private function syncUserAccess(User $user, Request $request): void
    {
        $permissions = $this->resolvePermissions($request);
        $role = $this->resolveRole($request, $permissions);

        if ($role === User::ROLE_SUPER_ADMIN) {
            $user->syncRoles([User::ROLE_SUPER_ADMIN]);
            $user->syncPermissions([]);

            return;
        }

        $user->syncRoles($role ? [$role] : []);
        $user->syncPermissions($permissions);
    }

    private function authorizeManage(User $target): void
    {
        if (! AdminPermissions::canManageUser(Auth::user(), $target)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola pengguna ini.');
        }
    }
}
