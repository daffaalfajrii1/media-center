<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()
            ->published()
            ->profil()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.pages.show', compact('page'));
    }
}
