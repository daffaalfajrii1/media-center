@php
    $logoFooterUrl = $websiteSettings->logoUrl() ?? asset('optilux/images/logo-white.webp');
@endphp

<footer class="section-dark">
    <div class="container">
        <div class="row gx-5">
            <div class="col-lg-4 col-sm-6">
                <img src="{{ $logoFooterUrl }}" class="logo-footer" alt="{{ $websiteSettings->siteName() }}">
                <div class="spacer-20"></div>
                <p>{{ $websiteSettings->footer_text ?: $websiteSettings->site_tagline ?: 'Website resmi pemerintah kecamatan.' }}</p>

                <div class="social-icons mb-sm-30">
                    @if ($websiteSettings->social_facebook)
                        <a href="{{ $websiteSettings->social_facebook }}" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a>
                    @endif
                    @if ($websiteSettings->social_instagram)
                        <a href="{{ $websiteSettings->social_instagram }}" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a>
                    @endif
                    @if ($websiteSettings->social_youtube)
                        <a href="{{ $websiteSettings->social_youtube }}" target="_blank" rel="noopener"><i class="fa-brands fa-youtube"></i></a>
                    @endif
                    @if ($websiteSettings->whatsappUrl())
                        <a href="{{ $websiteSettings->whatsappUrl() }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i></a>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 col-sm-12 order-lg-1 order-sm-2">
                <div class="row">
                    <div class="col-md-6">
                        <div class="widget">
                            <h5>Informasi</h5>
                            <ul>
                                <li><a href="{{ route('public.berita.index') }}">Berita</a></li>
                                <li><a href="{{ route('public.agenda.index') }}">Agenda</a></li>
                                <li><a href="{{ route('public.galeri.index') }}">Galeri</a></li>
                                <li><a href="{{ route('public.pengumuman.index') }}">Pengumuman</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="widget">
                            <h5>Layanan</h5>
                            <ul>
                                <li><a href="{{ route('public.layanan.index') }}">Layanan Publik</a></li>
                                <li><a href="{{ route('public.layanan.check-status') }}">Cek Status Pengajuan</a></li>
                                <li><a href="{{ route('public.pegawai.index') }}">Struktur Pegawai</a></li>
                                <li><a href="{{ route('public.kontak.index') }}">Kontak</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 order-lg-2 order-sm-1">
                <div class="widget">
                    <h5>Hubungi Kami</h5>
                    @if ($websiteSettings->contact_address)
                        <div class="fw-bold text-white"><i class="icofont-location-pin me-2 id-color"></i>Alamat</div>
                        {{ $websiteSettings->contact_address }}
                        <div class="spacer-20"></div>
                    @endif
                    @if ($websiteSettings->contact_phone)
                        <div class="fw-bold text-white"><i class="icofont-phone me-2 id-color"></i>Telepon</div>
                        {{ $websiteSettings->contact_phone }}
                        <div class="spacer-20"></div>
                    @endif
                    @if ($websiteSettings->contact_email)
                        <div class="fw-bold text-white"><i class="icofont-envelope me-2 id-color"></i>Email</div>
                        <a href="mailto:{{ $websiteSettings->contact_email }}" class="text-white">{{ $websiteSettings->contact_email }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="subfooter">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="de-flex">
                        <div class="de-flex-col">
                            <div>&copy; {{ date('Y') }} {{ $websiteSettings->siteName() }}. Hak cipta dilindungi.</div>
                            <div class="kecamatan-footer-kominfo">
                                Dikembangkan dalam kolaborasi dengan
                                <span class="kecamatan-footer-kominfo__name">Dinas Komunikasi dan Informatika (Kominfo)</span>
                            </div>
                        </div>
                        <ul class="menu-simple">
                            <li><a href="{{ route('login') }}">Login Admin</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
