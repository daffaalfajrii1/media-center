<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Pengumuman;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $heroBeritas = Berita::query()
            ->published()
            ->with(['category', 'author.roles'])
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $featuredBeritas = Berita::query()
            ->published()
            ->with(['category', 'author.roles'])
            ->orderByDesc('published_at')
            ->limit(8)
            ->get();

        if ($featuredBeritas->count() >= 3) {
            $spotlightBeritas = $featuredBeritas->take(2);
            $featuredSliderBeritas = $featuredBeritas->slice(2)->values();
        } elseif ($featuredBeritas->count() === 2) {
            $spotlightBeritas = $featuredBeritas;
            $featuredSliderBeritas = collect();
        } else {
            $spotlightBeritas = collect();
            $featuredSliderBeritas = $featuredBeritas;
        }

        $latestBeritas = $featuredBeritas->take(6);

        $upcomingAgendas = Agenda::query()
            ->published()
            ->upcoming()
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $latestPengumumans = Pengumuman::query()
            ->published()
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $featuredGaleris = Galeri::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $totalPegawai = Pegawai::query()->published()->count();
        $totalJabatan = Jabatan::query()->active()->count();

        return view('public.home', compact(
            'heroBeritas',
            'latestBeritas',
            'spotlightBeritas',
            'featuredSliderBeritas',
            'upcomingAgendas',
            'latestPengumumans',
            'featuredGaleris',
            'totalPegawai',
            'totalJabatan',
        ));
    }
}
