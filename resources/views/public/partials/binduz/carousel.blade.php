@php
    $fallbackHero = asset('binduz/assets/images/hero-1.jpg');
    $heroAutoplayMs = $websiteSettings->carouselDurationMs();
    $hasCarousel = $websiteCarousels->isNotEmpty();
@endphp

@if ($hasCarousel)
    <section class="binduz-er-hero-carousel" data-autoplay="{{ $heroAutoplayMs }}" data-slide-count="{{ $websiteCarousels->count() }}">
        <div class="hero-slide-active">
            @foreach ($websiteCarousels as $slide)
                <div class="binduz-er-hero-slide {{ ($slide->title || $slide->caption) ? 'binduz-er-hero-slide--has-text' : 'binduz-er-hero-slide--image-only' }}">
                    <div class="binduz-er-hero-slide__media" style="--hero-bg: url('{{ $slide->imageUrl() }}')"></div>
                    @if ($slide->title || $slide->caption)
                        <div class="binduz-er-hero-slide__overlay">
                            <div class="container">
                                <div class="binduz-er-hero-slide__content">
                                    @if ($slide->title)
                                        <h2 class="binduz-er-hero-slide__title">
                                            @if ($slide->link_url)
                                                <a href="{{ $slide->link_url }}" target="_blank" rel="noopener">{{ $slide->title }}</a>
                                            @else
                                                {{ $slide->title }}
                                            @endif
                                        </h2>
                                    @endif
                                    @if ($slide->caption)
                                        <p class="binduz-er-hero-slide__caption mb-0">{{ $slide->caption }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
@else
    <section class="binduz-er-hero-welcome">
        <div class="binduz-er-hero-welcome__media" style="--hero-bg: url('{{ $fallbackHero }}')"></div>
        <div class="binduz-er-hero-welcome__overlay">
            <div class="container">
                <div class="binduz-er-hero-welcome__content">
                    <h1 class="binduz-er-hero-welcome__title">{{ $websiteSettings->siteName() }}</h1>
                    @if ($websiteSettings->site_tagline)
                        <p class="binduz-er-hero-welcome__tagline mb-0">{{ $websiteSettings->site_tagline }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif

@push('scripts')
@if ($hasCarousel)
<script>
(function ($) {
    function initHeroCarousel() {
        var $wrap = $('.binduz-er-hero-carousel');
        if (!$wrap.length) return;

        var $hero = $wrap.find('.hero-slide-active');
        var autoplay = parseInt($wrap.data('autoplay'), 10) || 5000;
        var slideCount = parseInt($wrap.data('slide-count'), 10) || $hero.children().length;
        var hasMultiple = slideCount > 1;
        var arrowPrev = '<button type="button" class="binduz-er-hero-carousel__arrow binduz-er-hero-carousel__arrow--prev" aria-label="Slide sebelumnya"><i class="fal fa-chevron-left"></i></button>';
        var arrowNext = '<button type="button" class="binduz-er-hero-carousel__arrow binduz-er-hero-carousel__arrow--next" aria-label="Slide berikutnya"><i class="fal fa-chevron-right"></i></button>';

        if ($hero.hasClass('slick-initialized')) {
            $hero.slick('unslick');
        }

        $hero.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            autoplay: hasMultiple,
            autoplaySpeed: autoplay,
            fade: true,
            dots: false,
            arrows: hasMultiple,
            prevArrow: arrowPrev,
            nextArrow: arrowNext,
            adaptiveHeight: false,
        });
    }

    $(initHeroCarousel);
})(jQuery);
</script>
@endif
@endpush
