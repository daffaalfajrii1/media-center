@php
    $fallbackImage = asset('binduz/assets/images/latest-news-1.jpg');
    $fallbackSpotlight = asset('binduz/assets/images/hero-1.jpg');
@endphp
@if ($featuredSliderBeritas->isNotEmpty() || $spotlightBeritas->isNotEmpty())
    <section class="binduz-er-featured-area binduz-er-home-featured">
        <div class="container">
            <div class="row">
                @if ($featuredSliderBeritas->isNotEmpty())
                    <div class="col-lg-4 col-md-6">
                        <div class="binduz-er-featured-slider mt-30">
                            <div class="binduz-er-featured-title d-flex justify-content-between align-items-center">
                                <h3 class="binduz-er-title mb-0">Berita Terbaru</h3>
                                <a href="{{ route('public.berita.index') }}" class="binduz-er-home-featured__link">Lihat Semua</a>
                            </div>
                            <div class="binduz-er-featured-slider-item">
                                @foreach ($featuredSliderBeritas as $berita)
                                    <div class="binduz-er-trending-news-list-box">
                                        <div class="binduz-er-thumb">
                                            <a href="{{ route('public.berita.show', $berita->slug) }}">
                                                <img src="{{ $berita->thumbnailUrl() ?? $fallbackImage }}" alt="{{ $berita->title }}">
                                            </a>
                                        </div>
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
                                                        <span><i class="fal fa-calendar-alt"></i> {{ $berita->published_at->translatedFormat('d F Y') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="binduz-er-trending-news-list-title">
                                                <h4 class="binduz-er-title">
                                                    <a href="{{ route('public.berita.show', $berita->slug) }}">{{ $berita->title }}</a>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if ($spotlightBeritas->isNotEmpty())
                    <div class="col-lg-5 col-md-6">
                        @foreach ($spotlightBeritas as $berita)
                            <div class="binduz-er-trending-news-item mb-30">
                                <a href="{{ route('public.berita.show', $berita->slug) }}">
                                    <img src="{{ $berita->thumbnailUrl() ?? $fallbackSpotlight }}" alt="{{ $berita->title }}">
                                </a>
                                <div class="binduz-er-trending-news-overlay">
                                    <div class="binduz-er-trending-news-meta">
                                        <div class="binduz-er-meta-categories">
                                            @if ($berita->category)
                                                <a href="{{ route('public.berita.index', ['kategori' => $berita->category->slug]) }}">{{ $berita->category->name }}</a>
                                            @else
                                                <span>{{ $berita->categoryName() }}</span>
                                            @endif
                                        </div>
                                        @if ($berita->published_at)
                                            <div class="binduz-er-meta-date">
                                                <span><i class="fal fa-calendar-alt"></i> {{ $berita->published_at->translatedFormat('d F Y') }}</span>
                                            </div>
                                        @endif
                                        <div class="binduz-er-trending-news-title">
                                            <h3 class="binduz-er-title">
                                                <a href="{{ route('public.berita.show', $berita->slug) }}">{{ $berita->title }}</a>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="binduz-er-news-share">
                                        <a href="{{ route('public.berita.show', $berita->slug) }}" title="Baca berita"><i class="fal fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="col-lg-3">
                    <div class="binduz-er-sidebar-social mt-30">
                        <div class="binduz-er-sidebar-title">
                            <h4 class="binduz-er-title">Media Sosial</h4>
                        </div>
                        @if ($websiteSettings->social_facebook || $websiteSettings->social_instagram || $websiteSettings->social_youtube)
                            <div class="binduz-er-social-list">
                                <div class="binduz-er-list">
                                    @if ($websiteSettings->social_facebook)
                                        <a href="{{ $websiteSettings->social_facebook }}" target="_blank" rel="noopener">
                                            <span><i class="fab fa-facebook-f"></i> Facebook</span>
                                            <span>Kunjungi</span>
                                        </a>
                                    @endif
                                    @if ($websiteSettings->social_instagram)
                                        <a href="{{ $websiteSettings->social_instagram }}" target="_blank" rel="noopener">
                                            <span><i class="fab fa-instagram"></i> Instagram</span>
                                            <span>Ikuti</span>
                                        </a>
                                    @endif
                                    @if ($websiteSettings->social_youtube)
                                        <a href="{{ $websiteSettings->social_youtube }}" target="_blank" rel="noopener">
                                            <span><i class="fab fa-youtube"></i> YouTube</span>
                                            <span>Subscribe</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @include('public.partials.binduz.berita-terkini-widget', [
                            'beritas' => $latestBeritas ?? collect(),
                            'limit' => 3,
                            'class' => 'mt-20',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
