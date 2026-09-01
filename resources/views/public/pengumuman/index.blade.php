@extends('public.layouts.binduz')

@section('title', 'Pengumuman')

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', ['items' => [['label' => 'Pengumuman']]])
@endsection

@section('content')
    <section class="binduz-er-page-content">
        <div class="container">
            <div class="binduz-er-section-title mb-30"><h3>Pengumuman</h3></div>
            <div class="row">
                @forelse ($pengumumans as $pengumuman)
                    <div class="col-lg-4 col-md-6 mb-30">
                        <div class="binduz-er-layanan-card h-100">
                            @if ($pengumuman->isPenting())
                                <span class="binduz-er-alert-penting">Penting</span>
                            @endif
                            @if ($pengumuman->is_featured)
                                <span class="badge bg-info mb-2">Unggulan</span>
                            @endif
                            <h4><a href="{{ route('public.pengumuman.show', $pengumuman->slug) }}">{{ $pengumuman->title }}</a></h4>
                            @if ($pengumuman->published_at)
                                <small class="text-muted d-block mb-2">{{ $pengumuman->published_at->translatedFormat('d F Y') }}</small>
                            @endif
                            <p class="mb-2">{{ $pengumuman->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($pengumuman->content), 140) }}</p>
                            <small class="text-muted">{{ $pengumuman->attachmentsCountLabel() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="binduz-er-layanan-card text-center"><p class="mb-0">Belum ada pengumuman aktif.</p></div>
                    </div>
                @endforelse
            </div>
            @if ($pengumumans->hasPages())
                <div class="binduz-er-pagination">{{ $pengumumans->links() }}</div>
            @endif
        </div>
    </section>
@endsection
