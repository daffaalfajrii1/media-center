@extends('public.layouts.binduz')

@section('title', $galeri->title)

@section('body_class', 'media-galeri-show-page')

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', [
        'items' => [
            ['label' => 'Galeri', 'url' => route('public.galeri.index')],
            ['label' => $galeri->title],
        ],
    ])
@endsection

@section('content')
    <section class="media-galeri-show">
        <div class="container">
            <header class="media-galeri-show__header">
                <h1 class="media-galeri-show__title">{{ $galeri->title }}</h1>
                <div class="media-galeri-show__meta">
                    @if ($galeri->event_date)
                        <span><i class="fal fa-calendar-alt"></i> {{ $galeri->event_date->translatedFormat('d F Y') }}</span>
                    @endif
                    <span><i class="fal fa-images"></i> {{ $galeri->itemsCountLabel() }}</span>
                </div>
                @if ($galeri->description)
                    <p class="media-galeri-show__desc">{{ $galeri->description }}</p>
                @endif
            </header>

            @if ($galeri->photos->isNotEmpty())
                <div class="media-galeri-show__viewer">
                    <div class="media-galeri-show__slider-wrap">
                        <div class="media-galeri-show__slider">
                            @foreach ($galeri->photos as $index => $photo)
                                <div class="media-galeri-show__slide">
                                    <a href="{{ $photo->imageUrl() }}"
                                       class="media-galeri-show__zoom"
                                       data-gallery="galeri-{{ $galeri->id }}"
                                       title="{{ $photo->title ?: $galeri->title }}">
                                        <img src="{{ $photo->imageUrl() }}" alt="{{ $photo->title ?: $galeri->title }}">
                                        <span class="media-galeri-show__zoom-hint">
                                            <i class="fal fa-expand"></i> Lihat foto
                                        </span>
                                    </a>
                                    @if ($photo->title)
                                        <p class="media-galeri-show__caption">{{ $photo->title }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if ($galeri->photos->count() > 1)
                            <div class="media-galeri-show__counter">
                                <span class="media-galeri-show__counter-current">1</span>
                                <span>/</span>
                                <span>{{ $galeri->photos->count() }}</span>
                            </div>
                        @endif
                    </div>

                    @if ($galeri->photos->count() > 1)
                        <div class="media-galeri-show__thumbs">
                            @foreach ($galeri->photos as $photo)
                                <div class="media-galeri-show__thumb">
                                    <img src="{{ $photo->imageUrl() }}" alt="{{ $photo->title ?: $galeri->title }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if ($galeri->videos->isNotEmpty())
                <div class="media-galeri-show__videos">
                    <h2 class="media-galeri-show__section-title">Video</h2>
                    <div class="row g-4">
                        @foreach ($galeri->videos as $video)
                            <div class="col-lg-6">
                                <article class="media-galeri-show__video-card">
                                    <div class="ratio ratio-16x9">
                                        <iframe src="{{ $video->embedUrl() }}" title="{{ $video->title ?: $galeri->title }}" allowfullscreen loading="lazy"></iframe>
                                    </div>
                                    @if ($video->title)
                                        <p class="media-galeri-show__video-title">{{ $video->title }}</p>
                                    @endif
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($galeri->photos->isEmpty() && $galeri->videos->isEmpty())
                <div class="binduz-er-layanan-card text-center">
                    <p class="mb-0">Belum ada media dalam galeri ini.</p>
                </div>
            @endif

            <div class="media-galeri-show__back">
                <a href="{{ route('public.galeri.index') }}" class="binduz-er-main-btn">
                    <i class="fal fa-arrow-left"></i> Kembali ke Galeri
                </a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function ($) {
    var $slider = $('.media-galeri-show__slider');
    if (!$slider.length) return;

    var $thumbs = $('.media-galeri-show__thumbs');
    var $counter = $('.media-galeri-show__counter-current');

    $slider.on('init reInit afterChange', function (event, slick, current) {
        var i = (typeof current === 'undefined' ? 0 : current) + 1;
        $counter.text(i);
    });

    $slider.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        dots: $thumbs.length === 0,
        infinite: true,
        adaptiveHeight: false,
        prevArrow: '<button type="button" class="media-galeri-show__arrow media-galeri-show__arrow--prev" aria-label="Foto sebelumnya"><i class="fal fa-chevron-left"></i></button>',
        nextArrow: '<button type="button" class="media-galeri-show__arrow media-galeri-show__arrow--next" aria-label="Foto berikutnya"><i class="fal fa-chevron-right"></i></button>',
        asNavFor: $thumbs.length ? '.media-galeri-show__thumbs' : null,
        responsive: [
            { breakpoint: 768, settings: { arrows: true, dots: $thumbs.length === 0 } }
        ]
    });

    if ($thumbs.length) {
        $thumbs.slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            asNavFor: '.media-galeri-show__slider',
            dots: false,
            arrows: false,
            centerMode: false,
            focusOnSelect: true,
            infinite: true,
            responsive: [
                { breakpoint: 992, settings: { slidesToShow: 4 } },
                { breakpoint: 576, settings: { slidesToShow: 3 } }
            ]
        });
    }

    $slider.magnificPopup({
        delegate: 'a.media-galeri-show__zoom',
        type: 'image',
        gallery: { enabled: true },
        mainClass: 'mfp-fade',
        removalDelay: 200,
        image: {
            titleSrc: function (item) {
                return item.el.attr('title') || '';
            }
        }
    });
})(jQuery);
</script>
@endpush
