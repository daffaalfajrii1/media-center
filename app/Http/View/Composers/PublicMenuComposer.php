<?php

namespace App\Http\View\Composers;

use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicMenuComposer
{
    public function compose(View $view): void
    {
        if (! Schema::hasTable('pages')) {
            $view->with('profilPages', collect());

            return;
        }

        $view->with(
            'profilPages',
            Page::query()
                ->published()
                ->profil()
                ->ordered()
                ->get(['id', 'title', 'slug'])
        );
    }
}
