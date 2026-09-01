@php
    $fallbackImage = asset('binduz/assets/images/latest-news-1.jpg');
    $variant = $variant ?? 'card';
    $cardClass = $variant === 'card' ? 'binduz-er-latest-news-item--card' : '';
@endphp
<div class="binduz-er-latest-news-item {{ $cardClass }} h-100">
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
        <div class="binduz-er-latest-news-title">
            <h4 class="binduz-er-title">
                <a href="{{ route('public.berita.show', $berita->slug) }}">{{ $berita->title }}</a>
            </h4>
        </div>
        <div class="binduz-er-meta-author">
            <div class="binduz-er-author">
                <span>Oleh <span>{{ $berita->authorName() }}</span></span>
            </div>
            <div class="binduz-er-meta-list">
                <ul>
                    <li><i class="fal fa-eye"></i> {{ number_format($berita->views_count) }}</li>
                </ul>
            </div>
        </div>
        @if ($berita->excerpt)
            <p class="binduz-er-excerpt">{{ $berita->excerpt }}</p>
        @endif
    </div>
</div>
