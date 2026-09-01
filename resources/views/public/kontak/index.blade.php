@extends('public.layouts.binduz')

@section('title', 'Kontak')

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', ['items' => [['label' => 'Kontak']]])
@endsection

@section('content')
    <section class="media-kontak-page">
        <div class="container">
            <div class="media-kontak-page__intro text-center">
                <h2 class="media-kontak-page__title">Hubungi Kami</h2>
                <p class="media-kontak-page__lead">
                    Tim {{ $websiteSettings->siteName() }} siap membantu informasi dan layanan publik.
                    Pilih kanal kontak di bawah untuk detail lengkap.
                </p>
            </div>

            <div class="row g-4 justify-content-center media-kontak-page__cards">
                @if ($websiteSettings->contact_address)
                    <div class="col-sm-6 col-lg-3">
                        <button type="button"
                                class="media-kontak-card"
                                data-bs-toggle="modal"
                                data-bs-target="#kontakDetailModal"
                                data-kontak-title="Alamat Kantor"
                                data-kontak-icon="fa-map-marker-alt"
                                data-kontak-value="{{ $websiteSettings->contact_address }}"
                                @if ($websiteSettings->mapsIframeSrc())
                                    data-kontak-map="{{ $websiteSettings->mapsIframeSrc() }}"
                                    data-kontak-action="https://www.google.com/maps/search/?api=1&query={{ urlencode($websiteSettings->contact_address) }}"
                                    data-kontak-action-label="Buka di Google Maps"
                                @endif>
                            <span class="media-kontak-card__icon media-kontak-card__icon--address" aria-hidden="true">
                                <i class="fal fa-map-marker-alt"></i>
                            </span>
                            <h3 class="media-kontak-card__title">Alamat</h3>
                            <p class="media-kontak-card__preview">{{ \Illuminate\Support\Str::limit($websiteSettings->contact_address, 72) }}</p>
                            <span class="media-kontak-card__cta">Lihat detail <i class="fal fa-arrow-right"></i></span>
                        </button>
                    </div>
                @endif

                @if ($websiteSettings->contact_phone)
                    <div class="col-sm-6 col-lg-3">
                        <button type="button"
                                class="media-kontak-card"
                                data-bs-toggle="modal"
                                data-bs-target="#kontakDetailModal"
                                data-kontak-title="Telepon"
                                data-kontak-icon="fa-phone"
                                data-kontak-value="{{ $websiteSettings->contact_phone }}"
                                data-kontak-action="tel:{{ preg_replace('/\s+/', '', $websiteSettings->contact_phone) }}"
                                data-kontak-action-label="Hubungi Sekarang">
                            <span class="media-kontak-card__icon media-kontak-card__icon--phone" aria-hidden="true">
                                <i class="fal fa-phone"></i>
                            </span>
                            <h3 class="media-kontak-card__title">Telepon</h3>
                            <p class="media-kontak-card__preview">{{ $websiteSettings->contact_phone }}</p>
                            <span class="media-kontak-card__cta">Lihat detail <i class="fal fa-arrow-right"></i></span>
                        </button>
                    </div>
                @endif

                @if ($websiteSettings->contact_email)
                    <div class="col-sm-6 col-lg-3">
                        <button type="button"
                                class="media-kontak-card"
                                data-bs-toggle="modal"
                                data-bs-target="#kontakDetailModal"
                                data-kontak-title="Email"
                                data-kontak-icon="fa-envelope"
                                data-kontak-value="{{ $websiteSettings->contact_email }}"
                                data-kontak-action="mailto:{{ $websiteSettings->contact_email }}"
                                data-kontak-action-label="Kirim Email">
                            <span class="media-kontak-card__icon media-kontak-card__icon--email" aria-hidden="true">
                                <i class="fal fa-envelope"></i>
                            </span>
                            <h3 class="media-kontak-card__title">Email</h3>
                            <p class="media-kontak-card__preview">{{ $websiteSettings->contact_email }}</p>
                            <span class="media-kontak-card__cta">Lihat detail <i class="fal fa-arrow-right"></i></span>
                        </button>
                    </div>
                @endif

                @if ($websiteSettings->whatsappUrl())
                    <div class="col-sm-6 col-lg-3">
                        <button type="button"
                                class="media-kontak-card"
                                data-bs-toggle="modal"
                                data-bs-target="#kontakDetailModal"
                                data-kontak-title="WhatsApp"
                                data-kontak-icon="fa-whatsapp"
                                data-kontak-value="Hubungi kami melalui WhatsApp untuk pertanyaan dan informasi layanan."
                                data-kontak-action="{{ $websiteSettings->whatsappUrl() }}"
                                data-kontak-action-label="Chat WhatsApp">
                            <span class="media-kontak-card__icon media-kontak-card__icon--whatsapp" aria-hidden="true">
                                <i class="fab fa-whatsapp"></i>
                            </span>
                            <h3 class="media-kontak-card__title">WhatsApp</h3>
                            <p class="media-kontak-card__preview">{{ $websiteSettings->contact_whatsapp }}</p>
                            <span class="media-kontak-card__cta">Lihat detail <i class="fal fa-arrow-right"></i></span>
                        </button>
                    </div>
                @endif
            </div>

            @if ($websiteSettings->social_facebook || $websiteSettings->social_instagram || $websiteSettings->social_youtube)
                <div class="media-kontak-page__social text-center">
                    <h4 class="media-kontak-page__social-title">Media Sosial</h4>
                    <div class="media-kontak-page__social-links">
                        @if ($websiteSettings->social_facebook)
                            <a href="{{ $websiteSettings->social_facebook }}" target="_blank" rel="noopener" class="media-kontak-social-btn media-kontak-social-btn--facebook" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if ($websiteSettings->social_instagram)
                            <a href="{{ $websiteSettings->social_instagram }}" target="_blank" rel="noopener" class="media-kontak-social-btn media-kontak-social-btn--instagram" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if ($websiteSettings->social_youtube)
                            <a href="{{ $websiteSettings->social_youtube }}" target="_blank" rel="noopener" class="media-kontak-social-btn media-kontak-social-btn--youtube" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>

    @include('public.partials.binduz.kontak-modal')
@endsection
