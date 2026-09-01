@extends('public.layouts.binduz')

@section('title', $berita->title)
@section('meta_description', $berita->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($berita->content), 160))
@section('og_type', 'article')
@section('og_image', $berita->socialShareImageUrl())
@section('canonical', $berita->publicUrl())
@if ($berita->published_at)
    @section('og_published_at', $berita->published_at->toIso8601String())
@endif

@section('body_class', 'gray-bg bg-2')

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', [
        'items' => [
            ['label' => 'Berita', 'url' => route('public.berita.index')],
            ['label' => $berita->title],
        ],
    ])
@endsection

@section('content')
    <section class="binduz-er-author-item-area pb-60 pt-40">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <article class="binduz-er-author-item mb-40">
                        @if ($berita->thumbnailUrl())
                            <div class="binduz-er-thumb">
                                <img src="{{ $berita->thumbnailUrl() }}" alt="{{ $berita->title }}">
                            </div>
                        @endif

                        <div class="binduz-er-content">
                            <div class="binduz-er-meta-item">
                                <div class="binduz-er-meta-categories">
                                    @if ($berita->category)
                                        <a href="{{ route('public.berita.index', ['kategori' => $berita->category->slug]) }}">{{ $berita->category->name }}</a>
                                    @else
                                        <span>{{ $berita->categoryName() }}</span>
                                    @endif
                                </div>
                                @if ($berita->published_at)
                                    <div class="binduz-er-meta-date">
                                        <span><i class="fal fa-calendar-alt"></i>{{ $berita->published_at->translatedFormat('d F Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            <h1 class="binduz-er-title">{{ $berita->title }}</h1>

                            <div class="binduz-er-meta-author">
                                <div class="binduz-er-author">
                                    @if ($berita->authorAvatarUrl())
                                        <img src="{{ $berita->authorAvatarUrl() }}" alt="{{ $berita->authorName() }}" class="binduz-er-author-avatar-sm">
                                    @else
                                        <span class="binduz-er-author-avatar-sm binduz-er-author-avatar-sm--initial">{{ $berita->authorInitial() }}</span>
                                    @endif
                                    <span>Oleh <span>{{ $berita->authorName() }}</span></span>
                                </div>
                                <div class="binduz-er-meta-list">
                                    <ul>
                                        <li><i class="fal fa-eye"></i> <span id="berita-views-count">{{ number_format($berita->views_count) }}</span></li>
                                        <li>
                                            <button type="button"
                                                id="berita-like-btn"
                                                class="binduz-er-like-btn {{ $hasLiked ? 'is-liked' : '' }}"
                                                data-url="{{ route('public.berita.like', $berita->slug) }}"
                                                data-liked="{{ $hasLiked ? '1' : '0' }}"
                                                title="{{ $hasLiked ? 'Sudah disukai' : 'Sukai berita ini' }}">
                                                <i class="{{ $hasLiked ? 'fas' : 'fal' }} fa-heart"></i>
                                                <span id="berita-likes-count">{{ number_format($berita->likes_count) }}</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="binduz-er-blog-details-box">
                            @if ($berita->excerpt)
                                <div class="binduz-er-text">
                                    <p class="lead">{{ $berita->excerpt }}</p>
                                </div>
                            @endif
                            <div class="binduz-er-text media-berita-content kecamatan-content">
                                @safeHtml($berita->content)
                            </div>
                        </div>

                        <div class="binduz-er-social-share-tag">
                            <div class="binduz-er-tag binduz-er-tag--article">
                                <span class="binduz-er-tag-label">Tag</span>
                                <ul>
                                    @if ($berita->category)
                                        <li><a href="{{ route('public.berita.index', ['kategori' => $berita->category->slug]) }}">#{{ $berita->category->slug }}</a></li>
                                    @endif
                                    <li>
                                        <a href="{{ route('public.berita.show', $berita->slug) }}" title="#{{ $berita->slug }}">
                                            #{{ \Illuminate\Support\Str::limit($berita->slug, 40) }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if ($prevBerita || $nextBerita)
                            <div class="binduz-er-blog-post-prev-next binduz-er-blog-post-prev-next--fixed">
                                @if ($prevBerita)
                                    <div class="binduz-er-post-prev-next binduz-er-post-prev-next--prev">
                                        <a href="{{ route('public.berita.show', $prevBerita->slug) }}">
                                            <span><i class="fal fa-arrow-left"></i> Berita Sebelumnya</span>
                                            <h4 class="binduz-er-title">{{ $prevBerita->title }}</h4>
                                        </a>
                                    </div>
                                @endif
                                @if ($nextBerita)
                                    <div class="binduz-er-post-prev-next binduz-er-post-prev-next--next">
                                        <a href="{{ route('public.berita.show', $nextBerita->slug) }}">
                                            <span>Berita Selanjutnya <i class="fal fa-arrow-right"></i></span>
                                            <h4 class="binduz-er-title">{{ $nextBerita->title }}</h4>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if ($related->isNotEmpty())
                            <div class="binduz-er-blog-related-post">
                                <div class="binduz-er-related-post-title">
                                    <h3 class="binduz-er-title">Berita Terkait</h3>
                                </div>
                                <div class="binduz-er-blog-related-post-slide">
                                    @foreach ($related as $item)
                                        <div class="binduz-er-video-post binduz-er-recently-viewed-item">
                                            @include('public.partials.binduz.berita-related-card', ['berita' => $item])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </article>
                </div>

                <div class="col-lg-3">
                    @include('public.partials.binduz.berita-sidebar', [
                        'berita' => $berita,
                        'latestBeritas' => $sidebarLatest,
                        'popularBeritas' => $sidebarPopular,
                    ])
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const likeBtn = document.getElementById('berita-like-btn');
    if (!likeBtn) return;

    const likesCountEl = document.getElementById('berita-likes-count');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    likeBtn.addEventListener('click', async function () {
        if (likeBtn.dataset.liked === '1' || likeBtn.disabled) return;

        likeBtn.disabled = true;

        try {
            const response = await fetch(likeBtn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const data = await response.json();

            if (response.ok && likesCountEl) {
                likesCountEl.textContent = new Intl.NumberFormat('id-ID').format(data.likes_count ?? 0);
            }

            if (data.liked) {
                likeBtn.dataset.liked = '1';
                likeBtn.classList.add('is-liked');
                likeBtn.title = 'Sudah disukai';
                const icon = likeBtn.querySelector('i');
                if (icon) {
                    icon.classList.remove('fal');
                    icon.classList.add('fas');
                }
            }
        } catch (e) {
            likeBtn.disabled = false;
        }
    });
});
</script>
@if ($websiteSettings->googleAnalyticsEnabled())
<script>
    if (typeof gtag === 'function') {
        gtag('event', 'view_article', {
            article_title: @json($berita->title),
            article_category: @json($berita->categoryName()),
            article_slug: @json($berita->slug),
        });
    }
</script>
@endif
@endpush
