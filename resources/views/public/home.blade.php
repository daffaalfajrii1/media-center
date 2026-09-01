@extends('public.layouts.binduz')



@section('title', 'Beranda')

@section('meta_description', $websiteSettings->site_tagline ?: 'Portal resmi '.$websiteSettings->siteName().' — berita, agenda, galeri, dan pengumuman terkini.')



@section('content')

    @include('public.partials.binduz.carousel')



    @include('public.partials.binduz.berita-featured-section', [

        'spotlightBeritas' => $spotlightBeritas,

        'featuredSliderBeritas' => $featuredSliderBeritas,

        'latestBeritas' => $latestBeritas,

    ])



    <section class="binduz-er-latest-news-area pt-30 pb-30">

        <div class="container">

            <div class="row mb-20">

                <div class="col-md-6 col-lg-3 mb-20">

                    <div class="binduz-er-layanan-card text-center h-100">

                        <h4>{{ $totalPegawai }}</h4>

                        <p>Keanggotaan</p>

                    </div>

                </div>

                <div class="col-md-6 col-lg-3 mb-20">

                    <div class="binduz-er-layanan-card text-center h-100">

                        <h4>{{ $upcomingAgendas->count() }}</h4>

                        <p>Agenda Mendatang</p>

                    </div>

                </div>

                <div class="col-md-6 col-lg-3 mb-20">

                    <div class="binduz-er-layanan-card text-center h-100">

                        <h4>{{ $latestBeritas->count() }}</h4>

                        <p>Berita Terbaru</p>

                    </div>

                </div>

                <div class="col-md-6 col-lg-3 mb-20">

                    <div class="binduz-er-layanan-card text-center h-100 d-flex align-items-center justify-content-center">

                        <a href="{{ route('public.agenda.index') }}" class="binduz-er-main-btn">Lihat Agenda</a>

                    </div>

                </div>

            </div>

        </div>

    </section>



    @if ($featuredGaleris->isNotEmpty())

        <section class="binduz-er-home-galeri binduz-er-page-content bg-light overflow-hidden">

            <div class="container">

                <div class="binduz-er-section-title binduz-er-home-galeri__header text-center mb-40">

                    <h3 class="mb-1">Galeri Kegiatan</h3>

                    <p class="binduz-er-home-galeri__subtitle mb-2">Dokumentasi foto dan video kegiatan terbaru</p>

                    <a href="{{ route('public.galeri.index') }}" class="binduz-er-home-featured__link">Semua Galeri</a>

                </div>

                <div class="row g-4 justify-content-center binduz-er-home-galeri__grid d-none d-lg-flex">

                    @foreach ($featuredGaleris as $galeri)

                        <div class="col-lg-4">

                            @include('public.partials.binduz.galeri-card', ['galeri' => $galeri])

                        </div>

                    @endforeach

                </div>

                <div class="binduz-er-home-galeri__slider d-lg-none">

                    @foreach ($featuredGaleris as $galeri)

                        <div class="binduz-er-home-galeri__slide">

                            @include('public.partials.binduz.galeri-card', ['galeri' => $galeri])

                        </div>

                    @endforeach

                </div>

            </div>

        </section>

    @endif



    <section class="binduz-er-home-agenda binduz-er-page-content overflow-hidden">

        <div class="container">

            <div class="binduz-er-section-title binduz-er-home-agenda__header text-center mb-40">

                <h3 class="mb-1">Agenda Mendatang</h3>

                <p class="binduz-er-home-agenda__subtitle mb-2">Jangan lewatkan kegiatan penting di {{ $websiteSettings->siteName() }}</p>

                <a href="{{ route('public.agenda.index') }}" class="binduz-er-home-featured__link">Lihat Semua Agenda</a>

            </div>

            @if ($upcomingAgendas->isNotEmpty())

                <div class="binduz-er-home-agenda__list d-none d-lg-block">

                    @foreach ($upcomingAgendas as $agenda)

                        <div class="binduz-er-home-agenda__list-item">

                            @include('public.partials.binduz.agenda-card', ['agenda' => $agenda])

                        </div>

                    @endforeach

                </div>

                <div class="binduz-er-home-agenda__slider d-lg-none">

                    @foreach ($upcomingAgendas as $agenda)

                        <div class="binduz-er-home-agenda__slide">

                            @include('public.partials.binduz.agenda-card', ['agenda' => $agenda])

                        </div>

                    @endforeach

                </div>

            @else

                <div class="binduz-er-home-agenda__empty text-center">

                    <p class="mb-3">Belum ada agenda mendatang saat ini.</p>

                    <a href="{{ route('public.agenda.index') }}" class="binduz-er-main-btn">Lihat Semua Agenda</a>

                </div>

            @endif

        </div>

    </section>



    @if ($latestPengumumans->isNotEmpty())

        <section class="binduz-er-page-content bg-light">

            <div class="container">

                <div class="binduz-er-section-title d-flex justify-content-between align-items-center mb-40">

                    <h3>Pengumuman</h3>

                    <a href="{{ route('public.pengumuman.index') }}" class="binduz-er-main-btn">Semua Pengumuman</a>

                </div>

                <div class="row">

                    @foreach ($latestPengumumans as $pengumuman)

                        <div class="col-lg-4 mb-30">

                            <div class="binduz-er-layanan-card h-100">

                                @if ($pengumuman->isPenting())

                                    <span class="binduz-er-alert-penting">Penting</span>

                                @endif

                                <h4><a href="{{ route('public.pengumuman.show', $pengumuman->slug) }}">{{ $pengumuman->title }}</a></h4>

                                <p class="mb-0">{{ $pengumuman->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($pengumuman->content), 120) }}</p>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </section>

    @endif



    @if ($websiteSettings->mapsIframeSrc())

        <section class="binduz-er-page-content bg-light">

            <div class="container">

                <div class="row align-items-center">

                    <div class="col-lg-4 mb-30">

                        <h3>Kantor</h3>

                        @if ($websiteSettings->contact_address)

                            <p><i class="fal fa-map-marker-alt"></i> {{ $websiteSettings->contact_address }}</p>

                        @endif

                        @if ($websiteSettings->contact_phone)

                            <p><i class="fal fa-phone"></i> {{ $websiteSettings->contact_phone }}</p>

                        @endif

                        <a href="{{ route('public.kontak.index') }}" class="binduz-er-main-btn mt-2">Hubungi Kami</a>

                    </div>

                    <div class="col-lg-8">

                        <iframe src="{{ $websiteSettings->mapsIframeSrc() }}" width="100%" height="350" style="border:0;" allowfullscreen loading="lazy" title="Peta {{ $websiteSettings->siteName() }}"></iframe>

                    </div>

                </div>

            </div>

        </section>

    @endif

@endsection



@push('scripts')

<script>

(function ($) {

    function initHomeSlider(selector, prevClass, nextClass, label) {

        var $slider = $(selector);

        if (!$slider.length || $slider.hasClass('slick-initialized')) return;



        $slider.slick({

            slidesToShow: 1,

            slidesToScroll: 1,

            dots: false,

            arrows: true,

            infinite: true,

            autoplay: true,

            autoplaySpeed: 4500,

            prevArrow: '<button type="button" class="binduz-er-home-slider__arrow binduz-er-home-slider__arrow--prev ' + prevClass + '" aria-label="' + label + ' sebelumnya"><i class="fal fa-chevron-left"></i></button>',

            nextArrow: '<button type="button" class="binduz-er-home-slider__arrow binduz-er-home-slider__arrow--next ' + nextClass + '" aria-label="' + label + ' berikutnya"><i class="fal fa-chevron-right"></i></button>',

        });

    }



    initHomeSlider('.binduz-er-home-galeri__slider', 'binduz-er-home-galeri__arrow--prev', 'binduz-er-home-galeri__arrow--next', 'Galeri');

    initHomeSlider('.binduz-er-home-agenda__slider', 'binduz-er-home-agenda__arrow--prev', 'binduz-er-home-agenda__arrow--next', 'Agenda');

})(jQuery);

</script>

@endpush

