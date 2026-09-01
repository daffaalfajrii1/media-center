@php $fallbackImage = asset('binduz/assets/images/editors-pack-thumb-1.jpg'); @endphp
<div class="binduz-er-latest-news-item binduz-er-related-card">
    <div class="binduz-er-thumb">
        <a href="{{ route('public.berita.show', $berita->slug) }}">
            <img src="{{ $berita->thumbnailUrl() ?? $fallbackImage }}" alt="{{ $berita->title }}">
        </a>
    </div>
    <div class="binduz-er-content">
        <div class="binduz-er-meta-item">
            @if ($berita->category)
                <div class="binduz-er-meta-categories">
                    <a href="{{ route('public.berita.index', ['kategori' => $berita->category->slug]) }}">{{ $berita->category->name }}</a>
                </div>
            @else
                <div class="binduz-er-meta-categories">
                    <span>{{ $berita->categoryName() }}</span>
                </div>
            @endif
            @if ($berita->published_at)
                <div class="binduz-er-meta-date">
                    <span><i class="fal fa-calendar-alt"></i>{{ $berita->published_at->translatedFormat('d F Y') }}</span>
                </div>
            @endif
        </div>
        <h5 class="binduz-er-title">
            <a href="{{ route('public.berita.show', $berita->slug) }}">{{ $berita->title }}</a>
        </h5>
    </div>
</div>
