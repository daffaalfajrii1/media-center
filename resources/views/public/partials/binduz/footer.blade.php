<footer class="binduz-er-footer-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="binduz-er-footer-widget-style-1">
                            <div class="binduz-er-footer-title"><h4 class="binduz-er-title">Informasi</h4></div>
                            <div class="binduz-er-footer-menu-list">
                                <ul>
                                    <li><a href="{{ route('public.berita.index') }}">Berita</a></li>
                                    <li><a href="{{ route('public.agenda.index') }}">Agenda</a></li>
                                    <li><a href="{{ route('public.galeri.index') }}">Galeri</a></li>
                                    <li><a href="{{ route('public.pengumuman.index') }}">Pengumuman</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="binduz-er-footer-widget-style-1">
                            <div class="binduz-er-footer-title"><h4 class="binduz-er-title">Layanan</h4></div>
                            <div class="binduz-er-footer-menu-list">
                                <ul>
                                    <li><a href="{{ route('public.layanan.index') }}">Layanan Publik</a></li>
                                    <li><a href="{{ route('public.layanan.check-status') }}">Cek Status</a></li>
                                    <li><a href="{{ route('public.pegawai.index') }}">Keanggotaan</a></li>
                                    <li><a href="{{ route('public.kontak.index') }}">Kontak</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="binduz-er-footer-widget-style-1">
                            <div class="binduz-er-footer-title"><h4 class="binduz-er-title">Kontak</h4></div>
                            <div class="binduz-er-footer-menu-list">
                                <ul>
                                    @if ($websiteSettings->contact_phone)
                                        <li><a href="tel:{{ $websiteSettings->contact_phone }}">{{ $websiteSettings->contact_phone }}</a></li>
                                    @endif
                                    @if ($websiteSettings->contact_email)
                                        <li><a href="mailto:{{ $websiteSettings->contact_email }}">{{ $websiteSettings->contact_email }}</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="binduz-er-footer-widget-info">
                    <div class="binduz-er-logo">
                        @include('public.partials.binduz.site-logo', ['variant' => 'footer'])
                    </div>
                    <div class="binduz-er-text">
                        <p>{{ $websiteSettings->footer_text ?: $websiteSettings->site_tagline ?: 'Website resmi media center.' }}</p>
                    </div>
                    <div class="binduz-er-social">
                        <ul>
                            @if ($websiteSettings->social_facebook)
                                <li><a href="{{ $websiteSettings->social_facebook }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a></li>
                            @endif
                            @if ($websiteSettings->social_instagram)
                                <li><a href="{{ $websiteSettings->social_instagram }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a></li>
                            @endif
                            @if ($websiteSettings->social_youtube)
                                <li><a href="{{ $websiteSettings->social_youtube }}" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="binduz-er-footer-copyright-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="binduz-er-copyright-text">
                    <p>&copy; {{ date('Y') }} <span>{{ $websiteSettings->siteName() }}</span>. Hak cipta dilindungi.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="binduz-er-copyright-menu float-lg-end float-none">
                    <ul>
                        <li><a href="{{ route('login') }}">Login Admin</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="binduz-er-back-to-top">
    <p>KE ATAS <i class="fal fa-long-arrow-right"></i></p>
</div>
