@php
    use App\Support\SeoUrl;

    $seoTitle = trim($__env->yieldContent('title', 'Beranda'));
    $siteName = $websiteSettings->siteName();
    $fullTitle = $seoTitle === 'Beranda' ? $siteName : "{$seoTitle} — {$siteName}";
    $description = trim($__env->yieldContent('meta_description', $websiteSettings->site_tagline ?: 'Media Center resmi '.$siteName));
    $description = \Illuminate\Support\Str::limit(strip_tags($description), 160, '');
    $canonical = SeoUrl::absolute(trim($__env->yieldContent('canonical', url()->current()))) ?? url()->current();
    $ogImage = SeoUrl::absolute(trim($__env->yieldContent('og_image', $websiteSettings->defaultShareImageUrl())))
        ?? $websiteSettings->defaultShareImageUrl();
    $ogType = trim($__env->yieldContent('og_type', 'website'));
    $ogPublishedAt = trim($__env->yieldContent('og_published_at', ''));
    $robots = trim($__env->yieldContent('robots', 'index, follow'));
@endphp
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:locale" content="id_ID">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:secure_url" content="{{ $ogImage }}">
<meta property="og:image:alt" content="{{ $seoTitle }}">
@if ($ogPublishedAt)
    <meta property="article:published_time" content="{{ $ogPublishedAt }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $ogImage }}">
<meta name="twitter:image:alt" content="{{ $seoTitle }}">
