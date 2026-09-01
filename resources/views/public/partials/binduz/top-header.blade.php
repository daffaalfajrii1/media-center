@php
    $terbaruBerita = \App\Models\Berita::query()
        ->published()
        ->orderByDesc('published_at')
        ->limit(8)
        ->get();
@endphp
<div class="binduz-er-top-header-area">
    <div class="container">
        <div class="row align-items-center g-2">
            <div class="col-lg-7 col-xl-8">
                <div class="binduz-er-topbar-terbaru" aria-label="Berita terbaru">
                    <span class="binduz-er-topbar-terbaru__label">
                        <i class="fas fa-bolt"></i> Terbaru
                    </span>
                    <div class="binduz-er-topbar-terbaru__track-wrap">
                        @if ($terbaruBerita->isNotEmpty())
                            <div class="binduz-er-topbar-terbaru__track">
                                @foreach ($terbaruBerita->concat($terbaruBerita) as $berita)
                                    <a href="{{ route('public.berita.show', $berita->slug) }}" class="binduz-er-topbar-terbaru__item" title="{{ $berita->title }}">
                                        {{ $berita->title }}
                                    </a>
                                    <span class="binduz-er-topbar-terbaru__sep" aria-hidden="true">•</span>
                                @endforeach
                            </div>
                        @else
                            <p class="binduz-er-topbar-terbaru__fallback mb-0">
                                {{ $websiteSettings->site_tagline ?: 'Media Center' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-xl-4">
                <div class="binduz-er-topbar-social d-flex justify-content-lg-end align-items-center">
                    <div class="binduz-er-news-social">
                        <ul>
                            @if ($websiteSettings->social_facebook)
                                <li><a href="{{ $websiteSettings->social_facebook }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a></li>
                            @endif
                            @if ($websiteSettings->social_instagram)
                                <li><a href="{{ $websiteSettings->social_instagram }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram"></i></a></li>
                            @endif
                            @if ($websiteSettings->social_youtube)
                                <li><a href="{{ $websiteSettings->social_youtube }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-youtube"></i></a></li>
                            @endif
                            @if ($websiteSettings->whatsappUrl())
                                <li><a href="{{ $websiteSettings->whatsappUrl() }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="binduz-er-news-subscribe-btn">
                        <a class="binduz-er-main-btn" href="{{ route('public.layanan.index') }}">Layanan Online</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
