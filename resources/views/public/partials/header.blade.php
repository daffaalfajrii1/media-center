<header class="kecamatan-public-header">
    <nav class="navbar navbar-expand-lg kecamatan-public-navbar">
        <div class="container">
            <a class="navbar-brand kecamatan-public-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                @if ($websiteSettings->logoUrl())
                    <img src="{{ $websiteSettings->logoUrl() }}" alt="{{ $websiteSettings->siteName() }}" class="kecamatan-public-brand__logo">
                @endif
                <span class="kecamatan-public-brand__text">
                    <span class="kecamatan-public-brand__name">{{ $websiteSettings->siteName() }}</span>
                    @if ($websiteSettings->site_tagline)
                        <small class="kecamatan-public-brand__tagline d-none d-md-block">{{ $websiteSettings->site_tagline }}</small>
                    @endif
                </span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#kecamatanPublicNav" aria-controls="kecamatanPublicNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="kecamatanPublicNav">
                @include('public.partials.nav-links')
            </div>
        </div>
    </nav>
    @include('public.partials.marquee')
</header>
