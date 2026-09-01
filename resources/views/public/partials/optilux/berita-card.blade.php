<div class="kecamatan-berita-card h-100">
    <a href="{{ route('public.berita.show', $berita->slug) }}" class="kecamatan-post-thumb mb-3">
        <img alt="{{ $berita->title }}" src="{{ $berita->thumbnailUrl() ?? asset('optilux/images/blog/1.webp') }}">
    </a>
    <div>
        @if ($berita->published_at)
            <small class="text-muted d-block mb-1">{{ $berita->published_at->translatedFormat('d M Y') }}</small>
        @endif
        @if ($berita->category)
            <small class="id-color d-block mb-1">{{ $berita->category->name }}</small>
        @endif
        <h4 class="kecamatan-berita-card__title">
            <a href="{{ route('public.berita.show', $berita->slug) }}">{{ $berita->title }}</a>
        </h4>
        <small class="text-muted d-block mb-2">
            <i class="icofont-user-alt-3 id-color me-1"></i>{{ $berita->authorName() }}
        </small>
        <p class="mb-0 small text-muted">{{ $berita->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($berita->content), 100) }}</p>
    </div>
</div>
