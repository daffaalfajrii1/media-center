@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Dashboard Kecamatan',
        'crumbs' => [['label' => 'Dashboard']],
    ])

    <div class="main-content">
        @if (($pengajuanSummary['pending'] ?? 0) > 0)
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <i class="feather-alert-circle me-2"></i>
                    <strong>{{ $pengajuanSummary['pending'] }} pengajuan layanan</strong> menunggu tindakan Anda.
                </div>
                <a href="{{ route('admin.layanan-pengajuan.index') }}" class="btn btn-sm btn-warning text-dark">
                    Proses Sekarang
                </a>
            </div>
        @endif

        <div class="row g-3 mb-4">
            @foreach ([
                ['key' => 'berita', 'label' => 'Berita', 'icon' => 'feather-file-text', 'soft' => 'primary', 'permission' => 'manage berita', 'route' => 'admin.berita.index'],
                ['key' => 'agenda', 'label' => 'Agenda', 'icon' => 'feather-calendar', 'soft' => 'info', 'permission' => 'manage agenda', 'route' => 'admin.agenda.index'],
                ['key' => 'galeri', 'label' => 'Galeri', 'icon' => 'feather-image', 'soft' => 'success', 'permission' => 'manage galeri', 'route' => 'admin.galeri.index'],
                ['key' => 'pengumuman', 'label' => 'Pengumuman', 'icon' => 'feather-bell', 'soft' => 'warning', 'permission' => 'manage pengumuman', 'route' => 'admin.pengumuman.index'],
                ['key' => 'layanan', 'label' => 'Layanan', 'icon' => 'feather-layers', 'soft' => 'secondary', 'permission' => 'manage layanan', 'route' => 'admin.layanan.index'],
                ['key' => 'pengajuan_pending', 'label' => 'Pengajuan Baru', 'icon' => 'feather-inbox', 'soft' => 'danger', 'permission' => 'manage layanan', 'route' => 'admin.layanan-pengajuan.index', 'subtitle' => 'total'],
                ['key' => 'profil', 'label' => 'Halaman Profil', 'icon' => 'feather-file', 'soft' => 'primary', 'permission' => 'manage profil', 'route' => 'admin.halaman.index'],
                ['key' => 'pengguna', 'label' => 'Pengguna', 'icon' => 'feather-users', 'soft' => 'danger', 'permission' => 'manage users', 'route' => 'admin.pengguna.index'],
                ['key' => 'roles', 'label' => 'Role', 'icon' => 'feather-shield', 'soft' => 'warning', 'permission' => 'manage roles', 'route' => 'admin.roles.index'],
            ] as $stat)
                @can($stat['permission'])
                    @php
                        $value = $stats[$stat['key']] ?? 0;
                        $subtitle = null;
                        if (($stat['subtitle'] ?? null) === 'total' && isset($stats['pengajuan_total'])) {
                            $subtitle = $stats['pengajuan_total'].' total';
                        }
                    @endphp
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6">
                        <a href="{{ route($stat['route']) }}" class="text-decoration-none text-reset">
                            <div class="card kecamatan-dash-stat h-100 border-0 shadow-sm">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="kecamatan-dash-stat__icon bg-soft-{{ $stat['soft'] }} text-{{ $stat['soft'] }}">
                                            <i class="{{ $stat['icon'] }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-muted fs-12 d-block text-truncate">{{ $stat['label'] }}</span>
                                            <span class="fs-22 fw-bolder lh-1">{{ number_format($value) }}</span>
                                            @if ($subtitle)
                                                <span class="text-muted fs-11 d-block mt-1">{{ $subtitle }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endcan
            @endforeach
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Selamat Datang, {{ $user->name }}</h5>
                        <span class="text-muted fs-12">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            Panel administrasi <strong>{{ $siteName }}</strong>.
                            Kelola konten website, layanan publik, dan konfigurasi sistem dari satu tempat.
                        </p>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="badge bg-soft-primary text-primary px-3 py-2">
                                Role: {{ $user->getRoleNames()->join(', ') ?: 'Tanpa role' }}
                            </span>
                            <span class="badge bg-soft-success text-success px-3 py-2">
                                {{ $user->getAllPermissions()->count() }} permission aktif
                            </span>
                        </div>

                        @if ($moduleSummaries !== [])
                            <h6 class="fw-semibold mb-3">Ringkasan Modul Konten</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 kecamatan-dash-table">
                                    <thead>
                                        <tr>
                                            <th>Modul</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Terbit / Aktif</th>
                                            <th class="text-center">Draft / Nonaktif</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($moduleSummaries as $module)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="kecamatan-dash-table__icon bg-soft-{{ $module['soft'] }} text-{{ $module['soft'] }}">
                                                            <i class="{{ $module['icon'] }}"></i>
                                                        </span>
                                                        <span class="fw-medium">{{ $module['label'] }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center fw-semibold">{{ $module['total'] }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-soft-success text-success">{{ $module['published'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-soft-secondary text-secondary">{{ $module['draft'] }}</span>
                                                </td>
                                                <td class="text-end">
                                                    @if (! empty($module['extra']))
                                                        <span class="text-muted fs-12 me-2">{{ $module['extra'] }}</span>
                                                    @endif
                                                    <a href="{{ route($module['route']) }}" class="btn btn-sm btn-light-brand">Kelola</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0">Akses Cepat</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $quickLinks = [
                                ['permission' => 'manage pengaturan', 'route' => 'admin.pengaturan.edit', 'icon' => 'feather-settings', 'label' => 'Pengaturan Website'],
                                ['permission' => 'manage profil', 'route' => 'admin.halaman.index', 'icon' => 'feather-file', 'label' => 'Halaman Profil'],
                                ['permission' => 'manage berita', 'route' => 'admin.berita.index', 'icon' => 'feather-file-text', 'label' => 'Berita'],
                                ['permission' => 'manage berita', 'route' => 'admin.berita-kategori.index', 'icon' => 'feather-tag', 'label' => 'Kategori Berita'],
                                ['permission' => 'manage agenda', 'route' => 'admin.agenda.index', 'icon' => 'feather-calendar', 'label' => 'Agenda Kegiatan'],
                                ['permission' => 'manage galeri', 'route' => 'admin.galeri.index', 'icon' => 'feather-image', 'label' => 'Galeri'],
                                ['permission' => 'manage pengumuman', 'route' => 'admin.pengumuman.index', 'icon' => 'feather-bell', 'label' => 'Pengumuman'],
                                ['permission' => 'manage layanan', 'route' => 'admin.layanan.index', 'icon' => 'feather-layers', 'label' => 'Layanan Publik'],
                                ['permission' => 'manage layanan', 'route' => 'admin.layanan-pengajuan.index', 'icon' => 'feather-inbox', 'label' => 'Pengajuan Layanan', 'badge' => $pengajuanSummary['pending'] ?? 0],
                                ['permission' => 'manage users', 'route' => 'admin.pengguna.index', 'icon' => 'feather-users', 'label' => 'Pengguna'],
                                ['permission' => 'manage roles', 'route' => 'admin.roles.index', 'icon' => 'feather-shield', 'label' => 'Role & Permission'],
                            ];
                        @endphp
                        <ul class="list-unstyled mb-0 kecamatan-quick-links">
                            @foreach ($quickLinks as $link)
                                @can($link['permission'])
                                    <li>
                                        <a href="{{ route($link['route']) }}" class="kecamatan-quick-link">
                                            <i class="{{ $link['icon'] }} me-2"></i> {{ $link['label'] }}
                                            @if (($link['badge'] ?? 0) > 0)
                                                <span class="badge bg-warning text-dark ms-auto">{{ $link['badge'] }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endcan
                            @endforeach
                        </ul>
                    </div>
                </div>

                @if ($pengajuanSummary)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0">Status Pengajuan</h5>
                        </div>
                        <div class="card-body">
                            <div class="kecamatan-dash-pengajuan">
                                @foreach ([
                                    ['key' => 'pending', 'label' => 'Menunggu', 'soft' => 'warning'],
                                    ['key' => 'processing', 'label' => 'Diproses', 'soft' => 'info'],
                                    ['key' => 'completed', 'label' => 'Selesai', 'soft' => 'success'],
                                    ['key' => 'rejected', 'label' => 'Ditolak', 'soft' => 'danger'],
                                ] as $item)
                                    <div class="kecamatan-dash-pengajuan__item">
                                        <span class="text-muted fs-13">{{ $item['label'] }}</span>
                                        <span class="badge bg-soft-{{ $item['soft'] }} text-{{ $item['soft'] }}">{{ $pengajuanSummary[$item['key']] }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('admin.layanan-pengajuan.index') }}" class="btn btn-sm btn-light-brand w-100 mt-3">
                                Lihat Semua Pengajuan
                            </a>
                        </div>
                    </div>
                @endif

                @if ($upcomingAgendas->isNotEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">Agenda Mendatang</h5>
                            @can('manage agenda')
                                <a href="{{ route('admin.agenda.index') }}" class="fs-12 text-primary text-decoration-none">Semua</a>
                            @endcan
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush kecamatan-dash-agenda">
                                @foreach ($upcomingAgendas as $agenda)
                                    <li class="list-group-item">
                                        <a href="{{ route('admin.agenda.edit', $agenda) }}" class="text-decoration-none text-reset">
                                            <div class="fw-medium text-truncate">{{ $agenda->title }}</div>
                                            <div class="text-muted fs-12">
                                                <i class="feather-calendar me-1"></i>{{ $agenda->event_date->translatedFormat('d M Y') }}
                                                @if ($agenda->start_time)
                                                    · {{ $agenda->formattedTimeRange() }}
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($recentActivities->isNotEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 kecamatan-dash-table">
                            <thead>
                                <tr>
                                    <th>Modul</th>
                                    <th>Judul / Registrasi</th>
                                    <th>Status</th>
                                    <th>Diperbarui</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentActivities as $activity)
                                    <tr>
                                        <td>
                                            <span class="kecamatan-dash-table__icon bg-soft-{{ $activity['soft'] }} text-{{ $activity['soft'] }}">
                                                <i class="{{ $activity['icon'] }}"></i>
                                            </span>
                                            <span class="ms-2 fs-13">{{ $activity['module'] }}</span>
                                        </td>
                                        <td class="fw-medium">{{ Str::limit($activity['title'], 60) }}</td>
                                        <td>
                                            <span class="badge bg-soft-{{ $activity['status_soft'] }} text-{{ $activity['status_soft'] }}">
                                                {{ $activity['status'] }}
                                            </span>
                                        </td>
                                        <td class="text-muted fs-13">{{ $activity['date']?->diffForHumans() }}</td>
                                        <td class="text-end">
                                            <a href="{{ $activity['route'] }}" class="btn btn-sm btn-light">Buka</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
