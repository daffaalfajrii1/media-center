@php
    $logoUrl = $websiteSettings->logoUrl();
    $lines = $websiteSettings->logoTextLines();
    $variant = $variant ?? 'default';
    $linkClass = $linkClass ?? '';
@endphp
<a href="{{ route('home') }}" class="binduz-er-site-logo binduz-er-site-logo--{{ $variant }} {{ $linkClass }}" aria-label="{{ $websiteSettings->siteName() }}">
    @if ($logoUrl)
        <img src="{{ $logoUrl }}" alt="{{ $websiteSettings->siteName() }}" class="binduz-er-site-logo__image">
    @else
        <span class="binduz-er-site-logo__mark" aria-hidden="true">
            <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" role="img">
                <circle cx="24" cy="24" r="23" fill="#E8F4FC" stroke="#004E87" stroke-width="2"/>
                <path d="M10 30c4-8 8-12 14-12s10 4 14 12" fill="none" stroke="#004E87" stroke-width="2.5" stroke-linecap="round"/>
                <path d="M14 24c3-5 6-7 10-7s7 2 10 7" fill="none" stroke="#3B9AE8" stroke-width="2" stroke-linecap="round"/>
                <path d="M18 18c2-3 4-4 6-4s4 1 6 4" fill="none" stroke="#E74D58" stroke-width="2" stroke-linecap="round"/>
                <circle cx="24" cy="34" r="2.5" fill="#004E87"/>
            </svg>
        </span>
        <span class="binduz-er-site-logo__text">
            <span class="binduz-er-site-logo__primary">{{ $lines['primary'] }}</span>
            <span class="binduz-er-site-logo__secondary">{{ $lines['secondary'] }}</span>
        </span>
    @endif
</a>
