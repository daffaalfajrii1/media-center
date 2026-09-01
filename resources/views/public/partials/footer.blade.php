<footer class="kecamatan-public-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="kecamatan-footer-brand mb-3">
                    @if ($websiteSettings->logoUrl())
                        <img src="{{ $websiteSettings->logoUrl() }}" alt="{{ $websiteSettings->siteName() }}" class="kecamatan-footer-brand__logo mb-2">
                    @endif
                    <h5 class="mb-1">{{ $websiteSettings->siteName() }}</h5>
                    @if ($websiteSettings->site_tagline)
                        <p class="mb-0 fs-14">{{ $websiteSettings->site_tagline }}</p>
                    @endif
                </div>
                @if ($websiteSettings->footer_text)
                    <p class="kecamatan-footer-text mb-0">{{ $websiteSettings->footer_text }}</p>
                @endif
            </div>

            <div class="col-lg-4">
                <h6 class="kecamatan-footer-title">Kontak</h6>
                <ul class="list-unstyled kecamatan-footer-contact mb-0">
                    @if ($websiteSettings->contact_address)
                        <li><i class="bi bi-geo-alt me-2"></i>{{ $websiteSettings->contact_address }}</li>
                    @endif
                    @if ($websiteSettings->contact_phone)
                        <li><i class="bi bi-telephone me-2"></i>{{ $websiteSettings->contact_phone }}</li>
                    @endif
                    @if ($websiteSettings->contact_email)
                        <li><i class="bi bi-envelope me-2"></i><a href="mailto:{{ $websiteSettings->contact_email }}">{{ $websiteSettings->contact_email }}</a></li>
                    @endif
                    @if ($websiteSettings->whatsappUrl())
                        <li><i class="bi bi-whatsapp me-2"></i><a href="{{ $websiteSettings->whatsappUrl() }}" target="_blank" rel="noopener">WhatsApp</a></li>
                    @endif
                </ul>
            </div>

            <div class="col-lg-4">
                <h6 class="kecamatan-footer-title">Media Sosial</h6>
                <div class="kecamatan-footer-social">
                    @if ($websiteSettings->social_facebook)
                        <a href="{{ $websiteSettings->social_facebook }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    @endif
                    @if ($websiteSettings->social_instagram)
                        <a href="{{ $websiteSettings->social_instagram }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if ($websiteSettings->social_youtube)
                        <a href="{{ $websiteSettings->social_youtube }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="kecamatan-public-footer__bottom">
        <div class="container text-center py-3">
            <p class="mb-0">&copy; {{ date('Y') }} {{ $websiteSettings->siteName() }}. Semua hak dilindungi.</p>
        </div>
    </div>
</footer>
