<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AdminPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderByRaw("CASE name
                WHEN 'super-admin' THEN 1
                WHEN 'admin' THEN 2
                WHEN 'editor' THEN 3
                WHEN 'operator' THEN 4
                ELSE 5 END")
            ->get()
            ->map(function (Role $role) {
                $role->users_count = User::role($role->name)->count();
                $role->permissions_count = $role->permissions()->count();

                return $role;
            });

        return view('admin.roles.index', compact('roles'));
    }

    public function edit(string $role): View
    {
        $role = Role::findByName($role, 'web');

        $actor = Auth::user();
        $isSuperAdminRole = $role->name === User::ROLE_SUPER_ADMIN;

        if ($isSuperAdminRole && ! $actor->hasRole(User::ROLE_SUPER_ADMIN)) {
            abort(403);
        }

        $assignablePermissions = AdminPermissions::assignableFor($actor);
        if ($isSuperAdminRole) {
            $assignablePermissions = Permission::query()->where('guard_name', 'web')->orderBy('name')->get();
        }

        return view('admin.roles.edit', [
            'role' => $role,
            'assignablePermissions' => $assignablePermissions,
            'permissionLabels' => AdminPermissions::LABELS,
            'selectedPermissions' => $role->permissions->pluck('name')->all(),
            'isReadonly' => $isSuperAdminRole,
        ]);
    }

    public function update(Request $request, string $role): RedirectResponse
    {
        $role = Role::findByName($role, 'web');

        if ($role->name === User::ROLE_SUPER_ADMIN) {
            if (! Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)) {
                abort(403);
            }

            $role->syncPermissions(Permission::all());

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role Super Admin memiliki akses penuh secara otomatis.');
        }

        if (! in_array($role->name, AdminPermissions::MANAGED_ROLES, true)) {
            abort(403, 'Role ini tidak dapat diubah.');
        }

        $requested = $request->input('permissions', []);
        if (! is_array($requested)) {
            $requested = [];
        }

        if (! in_array('view dashboard', $requested, true)) {
            $requested[] = 'view dashboard';
        }

        $permissions = AdminPermissions::sanitizeForActor(Auth::user(), $requested);
        $role->syncPermissions($permissions);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Hak akses role '.AdminPermissions::roleLabel($role->name).' berhasil diperbarui.');
    }
}
