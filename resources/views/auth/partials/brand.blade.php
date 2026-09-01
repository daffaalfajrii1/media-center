@php
    $logoUrl = $websiteSettings->logoUrl();
@endphp
<div class="kecamatan-auth-brand">
    <a href="{{ route('home') }}" class="kecamatan-auth-brand__link">
        @if ($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $websiteSettings->siteName() }}" class="kecamatan-auth-brand__logo">
        @else
            <span class="kecamatan-auth-brand__fallback">{{ \Illuminate\Support\Str::substr($websiteSettings->siteName(), 0, 1) }}</span>
        @endif
    </a>
    <div class="kecamatan-auth-brand__name">{{ $websiteSettings->siteName() }}</div>
</div>
