<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\BeritaCategory;
use App\Models\WebsiteSetting;
use App\Services\BeritaAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BeritaStatistikController extends Controller
{
    public function __construct(private BeritaAnalyticsService $analytics)
    {
    }

    public function index(Request $request): View
    {
        $range = $this->analytics->normalizeRange((int) $request->integer('range', 30));
        $dashboard = $this->analytics->dashboard($range);

        $query = Berita::query()
            ->with(['category', 'author'])
            ->when($request->filled('berita_category_id'), fn ($q) => $q->where('berita_category_id', $request->berita_category_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));

        $beritas = (clone $query)
            ->orderByDesc('views_count')
            ->orderByDesc('published_at')
            ->paginate(20)
            ->withQueryString();

        $categories = BeritaCategory::query()->where('is_active', true)->orderBy('name')->get();
        $settings = WebsiteSetting::current();

        return view('admin.berita.statistik', compact(
            'beritas',
            'categories',
            'dashboard',
            'range',
            'settings',
        ));
    }
}
