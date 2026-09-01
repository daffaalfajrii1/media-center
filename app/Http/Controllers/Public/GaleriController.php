<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GaleriController extends Controller
{
    public function index(Request $request): View
    {
        $galeris = Galeri::query()
            ->published()
            ->when($request->search, fn ($q, $search) => $q->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->orderByDesc('is_featured')
            ->orderByDesc('event_date')
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.galeri.index', compact('galeris'));
    }

    public function show(string $slug): View
    {
        $galeri = Galeri::query()
            ->published()
            ->with(['photos', 'videos'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.galeri.show', compact('galeri'));
    }
}
