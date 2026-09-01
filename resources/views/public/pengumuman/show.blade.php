@extends('public.layouts.binduz')

@section('title', $pengumuman->title)

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', [
        'items' => [
            ['label' => 'Pengumuman', 'url' => route('public.pengumuman.index')],
            ['label' => $pengumuman->title],
        ],
    ])
@endsection

@section('content')
    <section class="binduz-er-page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-3">
                        @if ($pengumuman->isPenting())
                            <span class="binduz-er-alert-penting me-2">Penting</span>
                        @endif
                        @if ($pengumuman->published_at)
                            <span class="text-muted"><i class="fal fa-calendar-alt"></i> {{ $pengumuman->published_at->translatedFormat('d F Y') }}</span>
                        @endif
                        @if ($pengumuman->valid_until)
                            <span class="text-muted ms-3">Berlaku hingga {{ $pengumuman->valid_until->translatedFormat('d F Y') }}</span>
                        @endif
                    </div>
                    <h2 class="mb-4">{{ $pengumuman->title }}</h2>
                    <div class="kecamatan-content">@safeHtml($pengumuman->content)</div>

                    @if ($pengumuman->attachments->isNotEmpty())
                        <div class="mt-5">
                            <h4 class="mb-3">Lampiran</h4>
                            @foreach ($pengumuman->attachments as $attachment)
                                <div class="binduz-er-layanan-card d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $attachment->displayTitle() }}</div>
                                        <small>{{ $attachment->formattedFileSize() }}</small>
                                    </div>
                                    <a href="{{ $attachment->downloadUrl() }}" class="binduz-er-main-btn" target="_blank" rel="noopener" download>Unduh</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
