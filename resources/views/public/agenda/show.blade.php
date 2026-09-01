@extends('public.layouts.binduz')

@section('title', $agenda->title)

@section('body_class', 'gray-bg bg-2')

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', [
        'items' => [
            ['label' => 'Agenda', 'url' => route('public.agenda.index')],
            ['label' => $agenda->title],
        ],
    ])
@endsection

@section('content')
    <section class="binduz-er-page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    @if ($agenda->thumbnailUrl())
                        <img src="{{ $agenda->thumbnailUrl() }}" class="w-100 mb-4" alt="{{ $agenda->title }}">
                    @endif
                    <h2 class="mb-4">{{ $agenda->title }}</h2>
                    <div class="binduz-er-layanan-card mb-4">
                        <div class="row g-3">
                            <div class="col-md-6"><strong>Tanggal</strong><br>{{ $agenda->formattedEventDate() }}</div>
                            @if ($agenda->formattedTimeRange())
                                <div class="col-md-6"><strong>Waktu</strong><br>{{ $agenda->formattedTimeRange() }}</div>
                            @endif
                            <div class="col-md-6"><strong>Lokasi</strong><br>{{ $agenda->locationDisplay() }}</div>
                            <div class="col-md-6"><strong>Jenis</strong><br>{{ $agenda->eventTypeLabel() }}</div>
                            @if ($agenda->organizer)
                                <div class="col-md-6"><strong>Penyelenggara</strong><br>{{ $agenda->organizer }}</div>
                            @endif
                            @if ($agenda->contact_name || $agenda->contact_phone)
                                <div class="col-md-6">
                                    <strong>Kontak</strong><br>
                                    {{ $agenda->contact_name }}
                                    @if ($agenda->contact_phone) — {{ $agenda->contact_phone }} @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="kecamatan-content">@safeHtml($agenda->content)</div>
                    <div class="mt-4 d-flex flex-wrap gap-2">
                        @if ($agenda->registration_url)
                            <a href="{{ $agenda->registration_url }}" class="binduz-er-main-btn" target="_blank" rel="noopener">Daftar Kegiatan</a>
                        @endif
                        @if ($agenda->is_online && $agenda->online_link)
                            <a href="{{ $agenda->online_link }}" class="binduz-er-main-btn" target="_blank" rel="noopener">Link Online</a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    @if ($related->isNotEmpty())
                        <div class="binduz-er-layanan-card">
                            <h4 class="mb-3">Agenda Lainnya</h4>
                            @foreach ($related as $item)
                                <div class="mb-3">
                                    <a href="{{ route('public.agenda.show', $item->slug) }}" class="fw-bold">{{ $item->title }}</a>
                                    <div class="small text-muted">{{ $item->formattedEventDate() }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
