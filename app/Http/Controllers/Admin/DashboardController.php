<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Layanan;
use App\Models\LayananPengajuan;
use App\Models\Page;
use App\Models\Pegawai;
use App\Models\Pengumuman;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('admin.dashboard', [
            'user' => $user,
            'siteName' => WebsiteSetting::current()->siteName(),
            'stats' => $this->buildStats($user),
            'moduleSummaries' => $this->buildModuleSummaries($user),
            'pengajuanSummary' => $user->can('manage layanan') ? $this->pengajuanSummary() : null,
            'upcomingAgendas' => $user->can('manage agenda')
                ? Agenda::query()->upcoming()->limit(5)->get()
                : collect(),
            'recentActivities' => $this->buildRecentActivities($user),
        ]);
    }

    /** @return array<string, int> */
    private function buildStats(User $user): array
    {
        $stats = [];

        if ($user->can('manage berita')) {
            $stats['berita'] = Berita::count();
        }
        if ($user->can('manage agenda')) {
            $stats['agenda'] = Agenda::count();
        }
        if ($user->can('manage galeri')) {
            $stats['galeri'] = Galeri::count();
        }
        if ($user->can('manage pengumuman')) {
            $stats['pengumuman'] = Pengumuman::count();
        }
        if ($user->can('manage layanan')) {
            $stats['layanan'] = Layanan::count();
            $stats['pengajuan_pending'] = LayananPengajuan::where('status', LayananPengajuan::STATUS_PENDING)->count();
            $stats['pengajuan_total'] = LayananPengajuan::count();
        }
        if ($user->can('manage profil')) {
            $stats['profil'] = Page::profil()->count();
        }
        if ($user->can('manage pegawai')) {
            $stats['pegawai'] = Pegawai::count();
        }
        if ($user->can('manage users')) {
            $stats['pengguna'] = User::count();
        }
        if ($user->can('manage roles')) {
            $stats['roles'] = Role::where('guard_name', 'web')->count();
        }

        return $stats;
    }

    /** @return list<array<string, mixed>> */
    private function buildModuleSummaries(User $user): array
    {
        $summaries = [];

        if ($user->can('manage berita')) {
            $summaries[] = [
                'label' => 'Berita',
                'icon' => 'feather-file-text',
                'soft' => 'primary',
                'route' => 'admin.berita.index',
                'total' => Berita::count(),
                'published' => Berita::published()->count(),
                'draft' => Berita::draft()->count(),
            ];
        }

        if ($user->can('manage agenda')) {
            $summaries[] = [
                'label' => 'Agenda Kegiatan',
                'icon' => 'feather-calendar',
                'soft' => 'info',
                'route' => 'admin.agenda.index',
                'total' => Agenda::count(),
                'published' => Agenda::published()->count(),
                'draft' => Agenda::where('status', Agenda::STATUS_DRAFT)->count(),
                'extra' => Agenda::query()->upcoming()->count().' mendatang',
            ];
        }

        if ($user->can('manage galeri')) {
            $summaries[] = [
                'label' => 'Galeri',
                'icon' => 'feather-image',
                'soft' => 'success',
                'route' => 'admin.galeri.index',
                'total' => Galeri::count(),
                'published' => Galeri::published()->count(),
                'draft' => Galeri::where('status', Galeri::STATUS_DRAFT)->count(),
            ];
        }

        if ($user->can('manage pengumuman')) {
            $summaries[] = [
                'label' => 'Pengumuman',
                'icon' => 'feather-bell',
                'soft' => 'warning',
                'route' => 'admin.pengumuman.index',
                'total' => Pengumuman::count(),
                'published' => Pengumuman::published()->count(),
                'draft' => Pengumuman::where('status', Pengumuman::STATUS_DRAFT)->count(),
            ];
        }

        if ($user->can('manage profil')) {
            $summaries[] = [
                'label' => 'Halaman Profil',
                'icon' => 'feather-file',
                'soft' => 'primary',
                'route' => 'admin.halaman.index',
                'total' => Page::profil()->count(),
                'published' => Page::profil()->published()->count(),
                'draft' => Page::profil()->where('is_published', false)->count(),
            ];
        }

        if ($user->can('manage layanan')) {
            $summaries[] = [
                'label' => 'Layanan Publik',
                'icon' => 'feather-layers',
                'soft' => 'secondary',
                'route' => 'admin.layanan.index',
                'total' => Layanan::count(),
                'published' => Layanan::where('is_active', true)->count(),
                'draft' => Layanan::where('is_active', false)->count(),
                'extra_label' => 'Aktif',
                'draft_label' => 'Nonaktif',
            ];
        }

        return $summaries;
    }

    /** @return array<string, int> */
    private function pengajuanSummary(): array
    {
        return [
            'pending' => LayananPengajuan::where('status', LayananPengajuan::STATUS_PENDING)->count(),
            'processing' => LayananPengajuan::where('status', LayananPengajuan::STATUS_PROCESSING)->count(),
            'completed' => LayananPengajuan::where('status', LayananPengajuan::STATUS_COMPLETED)->count(),
            'rejected' => LayananPengajuan::where('status', LayananPengajuan::STATUS_REJECTED)->count(),
            'total' => LayananPengajuan::count(),
        ];
    }

    private function buildRecentActivities(User $user): Collection
    {
        $activities = collect();

        if ($user->can('manage berita')) {
            Berita::query()
                ->latest('updated_at')
                ->limit(4)
                ->get(['id', 'title', 'slug', 'status', 'updated_at'])
                ->each(function (Berita $item) use ($activities) {
                    $activities->push($this->activityItem(
                        module: 'Berita',
                        icon: 'feather-file-text',
                        soft: 'primary',
                        title: $item->title,
                        status: $item->isPublished() ? 'Terbit' : 'Draft',
                        statusSoft: $item->isPublished() ? 'success' : 'secondary',
                        date: $item->updated_at,
                        route: route('admin.berita.edit', $item),
                    ));
                });
        }

        if ($user->can('manage agenda')) {
            Agenda::query()
                ->latest('updated_at')
                ->limit(4)
                ->get(['id', 'title', 'status', 'updated_at'])
                ->each(function (Agenda $item) use ($activities) {
                    $activities->push($this->activityItem(
                        module: 'Agenda',
                        icon: 'feather-calendar',
                        soft: 'info',
                        title: $item->title,
                        status: $item->statusLabel(),
                        statusSoft: $item->isPublished() ? 'success' : 'secondary',
                        date: $item->updated_at,
                        route: route('admin.agenda.edit', $item),
                    ));
                });
        }

        if ($user->can('manage pengumuman')) {
            Pengumuman::query()
                ->latest('updated_at')
                ->limit(4)
                ->get(['id', 'title', 'status', 'updated_at'])
                ->each(function (Pengumuman $item) use ($activities) {
                    $activities->push($this->activityItem(
                        module: 'Pengumuman',
                        icon: 'feather-bell',
                        soft: 'warning',
                        title: $item->title,
                        status: $item->isPublished() ? 'Terbit' : 'Draft',
                        statusSoft: $item->isPublished() ? 'success' : 'secondary',
                        date: $item->updated_at,
                        route: route('admin.pengumuman.edit', $item),
                    ));
                });
        }

        if ($user->can('manage layanan')) {
            LayananPengajuan::query()
                ->with('layanan:id,title')
                ->latest('submitted_at')
                ->limit(5)
                ->get()
                ->each(function (LayananPengajuan $item) use ($activities) {
                    $statusSoft = match ($item->status) {
                        LayananPengajuan::STATUS_PENDING => 'warning',
                        LayananPengajuan::STATUS_PROCESSING => 'info',
                        LayananPengajuan::STATUS_COMPLETED => 'success',
                        LayananPengajuan::STATUS_REJECTED => 'danger',
                        default => 'secondary',
                    };

                    $activities->push($this->activityItem(
                        module: 'Pengajuan',
                        icon: 'feather-inbox',
                        soft: 'secondary',
                        title: $item->registration_number.' — '.($item->layanan?->title ?? 'Layanan'),
                        status: $item->statusLabel(),
                        statusSoft: $statusSoft,
                        date: $item->submitted_at ?? $item->created_at,
                        route: route('admin.layanan-pengajuan.show', $item),
                    ));
                });
        }

        return $activities
            ->sortByDesc(fn (array $item) => $item['date'])
            ->take(10)
            ->values();
    }

    /** @return array<string, mixed> */
    private function activityItem(
        string $module,
        string $icon,
        string $soft,
        string $title,
        string $status,
        string $statusSoft,
        $date,
        string $route,
    ): array {
        return [
            'module' => $module,
            'icon' => $icon,
            'soft' => $soft,
            'title' => $title,
            'status' => $status,
            'status_soft' => $statusSoft,
            'date' => $date,
            'route' => $route,
        ];
    }
}
