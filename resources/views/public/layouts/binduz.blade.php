@php $binduz = asset('binduz/assets'); @endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Beranda') — {{ $websiteSettings->siteName() }}</title>
    @include('public.partials.binduz.seo-meta')
    @include('public.partials.binduz.google-analytics')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ $websiteSettings->faviconUrl() ?? $binduz.'/images/favicon.ico' }}" type="image/png">
    <link rel="stylesheet" href="{{ $binduz }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ $binduz }}/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ $binduz }}/css/nice-select.css">
    <link rel="stylesheet" href="{{ $binduz }}/css/magnific-popup.css">
    <link rel="stylesheet" href="{{ $binduz }}/css/slick.css">
    <link rel="stylesheet" href="{{ $binduz }}/css/default.css">
    <link rel="stylesheet" href="{{ $binduz }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('css/public-binduz.css') }}">
    @stack('styles')
</head>
<body class="binduz-er-public @yield('body_class')">
    @include('public.partials.binduz.offcanvas')
    @include('public.partials.binduz.search')
    @include('public.partials.binduz.top-header')
    @include('public.partials.binduz.marquee')
    @include('public.partials.binduz.header')

    @hasSection('breadcrumb')
        @yield('breadcrumb')
    @endif

    @if (session('success'))
        <div class="container mt-3">
            <div class="alert alert-success mb-0">{{ session('success') }}</div>
        </div>
    @endif
    @if (session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger mb-0">{{ session('error') }}</div>
        </div>
    @endif

    <main class="binduz-er-site-main">
        @yield('content')
    </main>

    @include('public.partials.binduz.footer')

    <script src="{{ $binduz }}/js/vendor/modernizr-3.6.0.min.js"></script>
    <script src="{{ $binduz }}/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="{{ $binduz }}/js/bootstrap.min.js"></script>
    <script src="{{ $binduz }}/js/popper.min.js"></script>
    <script src="{{ $binduz }}/js/slick.min.js"></script>
    <script src="{{ $binduz }}/js/jquery.nice-select.min.js"></script>
    <script src="{{ $binduz }}/js/isotope.pkgd.min.js"></script>
    <script src="{{ $binduz }}/js/imagesloaded.pkgd.min.js"></script>
    <script src="{{ $binduz }}/js/jquery.magnific-popup.min.js"></script>
    <script src="{{ $binduz }}/js/main.js"></script>
    @include('public.partials.binduz.mobile-fixes')
    @stack('scripts')
</body>
</html>
