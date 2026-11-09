<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    public function index(): View
    {
        $tokens = PersonalAccessToken::query()
            ->with('tokenable')
            ->latest()
            ->paginate(15);

        return view('admin.api-tokens.index', [
            'tokens' => $tokens,
            'apiBaseUrl' => rtrim((string) config('app.url'), '/').'/api/v1',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ], [], [
            'name' => 'nama token',
        ]);

        $token = $request->user()->createToken($validated['name']);

        return redirect()
            ->route('admin.api-tokens.index')
            ->with('success', 'Token API berhasil dibuat. Salin sekarang — nilai lengkap tidak bisa dilihat lagi.')
            ->with('api_token_plain', $token->plainTextToken)
            ->with('api_token_name', $validated['name']);
    }

    public function destroy(PersonalAccessToken $apiToken): RedirectResponse
    {
        $apiToken->delete();

        return redirect()
            ->route('admin.api-tokens.index')
            ->with('success', 'Token API berhasil dicabut.');
    }
}
