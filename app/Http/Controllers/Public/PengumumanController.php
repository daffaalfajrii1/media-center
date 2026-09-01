<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    public function index(): View
    {
        $pengumumans = Pengumuman::query()
            ->published()
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('public.pengumuman.index', compact('pengumumans'));
    }

    public function show(string $slug): View
    {
        $pengumuman = Pengumuman::query()
            ->published()
            ->with('attachments')
            ->where('slug', $slug)
            ->firstOrFail();

        if ($pengumuman->isExpired()) {
            abort(404);
        }

        return view('public.pengumuman.show', compact('pengumuman'));
    }
}
