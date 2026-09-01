@extends('admin.layouts.app')

@section('title', 'Statistik Berita')

@push('styles')
<style>
    .berita-analytics-kpi {
        border: 1px solid #e9ecef;
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        height: 100%;
        background: #fff;
    }
    .berita-analytics-kpi__label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 600;
    }
    .berita-analytics-kpi__value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        margin-top: 0.35rem;
    }
    .berita-analytics-kpi__meta {
        font-size: 12px;
        color: #6c757d;
        margin-top: 0.35rem;
    }
    .berita-analytics-range .btn {
        border-radius: 999px;
        padding: 0.35rem 0.9rem;
        font-size: 13px;
        font-weight: 600;
    }
    .berita-analytics-chart-wrap {
        position: relative;
        min-height: 320px;
    }
    .berita-analytics-ga-card {
        border-left: 4px solid #4285f4;
    }
</style>
@endpush

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Statistik Berita',
        'crumbs' => [
            ['label' => 'Berita', 'url' => route('admin.berita.index')],
            ['label' => 'Statistik'],
        ],
        'actions' => '
            <a href="'.route('admin.berita.index').'" class="btn btn-light-brand">
                <i class="feather-arrow-left me-2"></i>Kembali ke Berita
            </a>
        ',
    ])

    <div class="main-content">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h5 class="mb-1">Analitik Pembacaan Berita</h5>
                <p class="text-muted mb-0 fs-13">Pantau tren kunjungan seperti Google Analytics. Data harian dicatat otomatis setiap artikel dibaca.</p>
            </div>
            <div class="btn-group berita-analytics-range" role="group" aria-label="Rentang waktu">
                @foreach ([7, 30, 90] as $option)
                    <a href="{{ route('admin.berita.statistik', array_merge(request()->except('page'), ['range' => $option])) }}"
                       class="btn {{ $range === $option ? 'btn-primary' : 'btn-light-brand' }}">
                        {{ $option }} hari
                    </a>
                @endforeach
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="berita-analytics-kpi">
                    <div class="berita-analytics-kpi__label">Kunjungan {{ $range }} hari</div>
                    <div class="berita-analytics-kpi__value text-primary">{{ number_format($dashboard['views_period']) }}</div>
                    <div class="berita-analytics-kpi__meta">
                        @if ($dashboard['change_percent'] !== null)
                            @if ($dashboard['change_percent'] >= 0)
                                <span class="text-success">+{{ $dashboard['change_percent'] }}%</span>
                            @else
                                <span class="text-danger">{{ $dashboard['change_percent'] }}%</span>
                            @endif
                            vs periode sebelumnya
                        @else
                            Periode sebelumnya: {{ number_format($dashboard['views_previous_period']) }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="berita-analytics-kpi">
                    <div class="berita-analytics-kpi__label">Hari ini</div>
                    <div class="berita-analytics-kpi__value text-success">{{ number_format($dashboard['views_today']) }}</div>
                    <div class="berita-analytics-kpi__meta">Pembacaan berita hari ini</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="berita-analytics-kpi">
                    <div class="berita-analytics-kpi__label">Total dibaca</div>
                    <div class="berita-analytics-kpi__value">{{ number_format($dashboard['total_views']) }}</div>
                    <div class="berita-analytics-kpi__meta">Akumulasi semua waktu</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="berita-analytics-kpi">
                    <div class="berita-analytics-kpi__label">Suka & artikel</div>
                    <div class="berita-analytics-kpi__value text-danger">{{ number_format($dashboard['total_likes']) }}</div>
                    <div class="berita-analytics-kpi__meta">{{ number_format($dashboard['published_count']) }} artikel terbit</div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tren Pembacaan</h5>
                        <span class="badge bg-soft-primary text-primary">{{ $range }} hari terakhir</span>
                    </div>
                    <div class="card-body">
                        <div class="berita-analytics-chart-wrap">
                            <canvas id="beritaViewsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm berita-analytics-ga-card h-100">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="feather-globe me-2 text-primary"></i>Google Analytics</h5>
                    </div>
                    <div class="card-body">
                        @if ($settings->googleAnalyticsEnabled())
                            <div class="mb-3">
                                <span class="badge bg-soft-success text-success">Terhubung</span>
                            </div>
                            <p class="text-muted fs-13 mb-2">Measurement ID aktif di website publik:</p>
                            <code class="d-inline-block mb-3">{{ $settings->googleAnalyticsMeasurementId() }}</code>
                            <p class="text-muted fs-13 mb-0">
                                Data lengkap (sumber traffic, perangkat, lokasi) tersedia di
                                <a href="https://analytics.google.com/" target="_blank" rel="noopener">Google Analytics</a>.
                            </p>
                        @else
                            <p class="text-muted fs-13">
                                Hubungkan website dengan Google Analytics 4 untuk melacak kunjungan halaman, sumber pengunjung, dan perilaku pengguna secara global.
                            </p>
                            <p class="text-muted fs-13 mb-3">
                                Statistik di panel ini fokus pada <strong>pembacaan berita internal</strong>. GA4 melengkapi data situs secara keseluruhan.
                            </p>
                            <a href="{{ route('admin.pengaturan.edit') }}#google-analytics" class="btn btn-primary btn-sm">
                                Atur Google Analytics
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($dashboard['top_beritas']->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">Berita Terpopuler ({{ $range }} hari)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th style="width:50px">#</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Periode</th>
                                    <th>Total</th>
                                    <th>Suka</th>
                                    <th>URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dashboard['top_beritas'] as $berita)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $berita->title }}</div>
                                            <div class="text-muted fs-12">{{ $berita->slug }}</div>
                                        </td>
                                        <td>{{ $berita->categoryName() }}</td>
                                        <td><span class="badge bg-soft-primary text-primary">{{ number_format((int) $berita->period_views) }}</span></td>
                                        <td>{{ number_format($berita->views_count) }}</td>
                                        <td>{{ number_format($berita->likes_count) }}</td>
                                        <td>
                                            <a href="{{ $berita->publicUrl() }}" target="_blank" rel="noopener" class="fs-12">/berita/{{ $berita->slug }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <input type="hidden" name="range" value="{{ $range }}">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Kategori</label>
                        <select name="berita_category_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('berita_category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                            <option value="published" @selected(request('status') === 'published')>Terbit</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-light-brand w-100">
                            <i class="feather-filter me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card kecamatan-table-card">
            <div class="card-header">
                <h5 class="mb-0">Semua Berita (urut dibaca)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Dilihat</th>
                                <th>Suka</th>
                                <th>Terbit</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($beritas as $berita)
                                <tr>
                                    <td>{{ $beritas->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $berita->title }}</div>
                                        <div class="text-muted fs-12">/berita/{{ $berita->slug }}</div>
                                    </td>
                                    <td>{{ $berita->categoryName() }}</td>
                                    <td>
                                        <span class="badge {{ $berita->isPublished() ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                            {{ $berita->isPublished() ? 'Terbit' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">{{ number_format($berita->views_count) }}</td>
                                    <td>{{ number_format($berita->likes_count) }}</td>
                                    <td>{{ $berita->published_at?->translatedFormat('d M Y') ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.berita.edit', $berita) }}" class="btn btn-sm btn-light-brand" title="Edit berita">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada data berita.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $beritas->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('beritaViewsChart');
    if (!canvas) return;

    const labels = @json($dashboard['labels']);
    const data = @json($dashboard['views']);

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pembacaan berita',
                data: data,
                borderColor: '#3454d1',
                backgroundColor: 'rgba(52, 84, 209, 0.12)',
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointHoverRadius: 5,
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return ' ' + context.parsed.y.toLocaleString('id-ID') + ' pembacaan';
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 12 },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        callback: function (value) {
                            return value.toLocaleString('id-ID');
                        },
                    },
                },
            },
        },
    });
});
</script>
@endpush
