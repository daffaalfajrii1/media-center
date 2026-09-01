<?php

namespace App\Services;

use App\Models\Berita;
use App\Models\BeritaDailyView;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BeritaAnalyticsService
{
    public const RANGE_OPTIONS = [7, 30, 90];

    public function recordView(Berita $berita): void
    {
        BeritaDailyView::record($berita->id);
    }

    public function normalizeRange(int $days): int
    {
        return in_array($days, self::RANGE_OPTIONS, true) ? $days : 30;
    }

    /**
     * @return array{
     *     range:int,
     *     labels:list<string>,
     *     views:list<int>,
     *     views_today:int,
     *     views_period:int,
     *     views_previous_period:int,
     *     change_percent:float|null,
     *     total_views:int,
     *     total_likes:int,
     *     published_count:int,
     *     top_beritas:Collection
     * }
     */
    public function dashboard(int $days = 30): array
    {
        $range = $this->normalizeRange($days);
        $start = now()->subDays($range - 1)->startOfDay();
        $end = now()->endOfDay();
        $previousStart = $start->copy()->subDays($range);
        $previousEnd = $start->copy()->subDay()->endOfDay();

        $dailyTotals = BeritaDailyView::query()
            ->whereDate('view_date', '>=', $start->toDateString())
            ->whereDate('view_date', '<=', $end->toDateString())
            ->get()
            ->groupBy(fn ($row) => Carbon::parse($row->view_date)->toDateString())
            ->map(fn ($rows) => (int) $rows->sum('views_count'));

        $labels = [];
        $views = [];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            $key = $date->toDateString();
            $labels[] = $date->translatedFormat('d M');
            $views[] = (int) ($dailyTotals[$key] ?? 0);
        }

        $viewsPeriod = array_sum($views);
        $viewsToday = (int) ($dailyTotals[now()->toDateString()] ?? 0);

        $viewsPreviousPeriod = (int) BeritaDailyView::query()
            ->whereDate('view_date', '>=', $previousStart->toDateString())
            ->whereDate('view_date', '<=', $previousEnd->toDateString())
            ->sum('views_count');

        $changePercent = null;
        if ($viewsPreviousPeriod > 0) {
            $changePercent = round((($viewsPeriod - $viewsPreviousPeriod) / $viewsPreviousPeriod) * 100, 1);
        }

        $periodStats = BeritaDailyView::query()
            ->whereDate('view_date', '>=', $start->toDateString())
            ->whereDate('view_date', '<=', $end->toDateString())
            ->selectRaw('berita_id, SUM(views_count) as period_views')
            ->groupBy('berita_id')
            ->orderByDesc('period_views')
            ->limit(10)
            ->get();

        if ($periodStats->isNotEmpty()) {
            $topBeritas = Berita::query()
                ->published()
                ->with('category')
                ->whereIn('id', $periodStats->pluck('berita_id'))
                ->get()
                ->sortByDesc(fn (Berita $berita) => (int) $periodStats->firstWhere('berita_id', $berita->id)?->period_views)
                ->values()
                ->each(function (Berita $berita) use ($periodStats): void {
                    $berita->period_views = (int) $periodStats->firstWhere('berita_id', $berita->id)?->period_views;
                });
        } else {
            $topBeritas = Berita::query()
                ->published()
                ->with('category')
                ->orderByDesc('views_count')
                ->limit(10)
                ->get()
                ->each(function (Berita $berita): void {
                    $berita->period_views = 0;
                });
        }

        return [
            'range' => $range,
            'labels' => $labels,
            'views' => $views,
            'views_today' => $viewsToday,
            'views_period' => $viewsPeriod,
            'views_previous_period' => $viewsPreviousPeriod,
            'change_percent' => $changePercent,
            'total_views' => (int) Berita::query()->sum('views_count'),
            'total_likes' => (int) Berita::query()->sum('likes_count'),
            'published_count' => Berita::query()->published()->count(),
            'top_beritas' => $topBeritas,
        ];
    }
}
