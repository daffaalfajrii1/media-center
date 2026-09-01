@extends('public.layouts.binduz')

@section('title', 'Berita')

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', ['items' => [['label' => 'Berita']]])
@endsection

@section('content')
    <section class="binduz-er-latest-news-area binduz-er-berita-index pt-60 pb-60">
        <div class="container">
            <div class="binduz-er-section-title mb-30">
                <h3>Semua Berita</h3>
                <p class="mb-0">Informasi dan kabar terkini dari {{ $websiteSettings->siteName() }}</p>
            </div>

            <form method="get" class="binduz-er-berita-filter row g-3 mb-40">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari berita..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="kategori" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('kategori') === $category->slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="binduz-er-main-btn w-100">Filter</button>
                </div>
            </form>

            <div class="row g-4">
                @forelse ($beritas as $berita)
                    <div class="col-lg-4 col-md-6">
                        @include('public.partials.binduz.berita-card', ['berita' => $berita, 'variant' => 'card'])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="binduz-er-layanan-card text-center py-5">
                            <p class="mb-0">Belum ada berita yang dipublikasikan.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($beritas->total() > 0)
                <p class="binduz-er-berita-index__summary text-muted text-center mt-4 mb-0">
                    Menampilkan {{ $beritas->firstItem() }}–{{ $beritas->lastItem() }} dari {{ $beritas->total() }} berita
                </p>
            @endif

            @if ($beritas->hasPages())
                <div class="binduz-er-pagination">{{ $beritas->onEachSide(1)->links() }}</div>
            @endif
        </div>
    </section>
@endsection
