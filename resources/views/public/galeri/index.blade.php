@extends('public.layouts.binduz')

@section('title', 'Galeri')

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', ['items' => [['label' => 'Galeri']]])
@endsection

@section('content')
    <section class="binduz-er-page-content binduz-er-galeri-index overflow-hidden">
        <div class="container">
            <div class="binduz-er-section-title binduz-er-galeri-index__header text-center mb-30">
                <h3>Galeri Kegiatan</h3>
                <p class="mb-0">Dokumentasi foto dan video kegiatan {{ $websiteSettings->siteName() }}</p>
            </div>

            <form method="get" class="binduz-er-galeri-filter row g-3 mb-40">
                <div class="col-md-8">
                    <input type="search"
                           name="search"
                           class="form-control"
                           placeholder="Cari galeri..."
                           value="{{ request('search') }}"
                           autocomplete="off">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="binduz-er-main-btn w-100">
                        <i class="fal fa-search"></i> Cari
                    </button>
                </div>
            </form>

            @if (request()->filled('search'))
                <p class="binduz-er-galeri-filter__result text-muted mb-4">
                    Menampilkan hasil untuk <strong>"{{ request('search') }}"</strong>
                    — {{ $galeris->total() }} galeri ditemukan.
                    <a href="{{ route('public.galeri.index') }}" class="ms-1">Reset</a>
                </p>
            @endif

            <div class="row g-4 justify-content-center binduz-er-galeri-index__grid">
                @forelse ($galeris as $galeri)
                    <div class="col-lg-4 col-md-6">
                        @include('public.partials.binduz.galeri-card', ['galeri' => $galeri, 'showDescription' => true])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="binduz-er-layanan-card text-center py-5">
                            <p class="mb-0">
                                @if (request()->filled('search'))
                                    Tidak ada galeri yang cocok dengan pencarian Anda.
                                @else
                                    Belum ada galeri yang dipublikasikan.
                                @endif
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($galeris->hasPages())
                <div class="binduz-er-pagination">{{ $galeris->links() }}</div>
            @endif
        </div>
    </section>
@endsection
