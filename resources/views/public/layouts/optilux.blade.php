<!DOCTYPE html>
<html lang="id">
<head>
    <title>@yield('title', 'Beranda') — {{ $websiteSettings->siteName() }}</title>
    <link rel="icon" href="{{ $websiteSettings->faviconUrl() ?? asset('optilux/images/icon.webp') }}" type="image/png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', $websiteSettings->site_tagline ?: 'Website resmi Kecamatan Sindang Kelingi')">
    <link href="{{ asset('optilux/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" id="bootstrap">
    <link href="{{ asset('optilux/css/plugins.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('optilux/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('optilux/css/coloring.css') }}" rel="stylesheet" type="text/css">
    <link id="colors" href="{{ asset('optilux/css/colors/scheme-01.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/public-optilux.css') }}" rel="stylesheet" type="text/css">
    @stack('styles')
</head>
<body @class(['page-beranda' => request()->routeIs('home')])>
    <div id="wrapper">
        <a href="#" id="back-to-top"></a>
        <div id="de-loader"></div>

        @include('public.partials.optilux.header')

        <main>
            <div id="top"></div>
            @if (session('success'))
                <div class="container mt-4">
                    <div class="alert alert-success mb-0">{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div class="container mt-4">
                    <div class="alert alert-danger mb-0">{{ session('error') }}</div>
                </div>
            @endif
            @yield('content')
        </main>

        @include('public.partials.optilux.footer')
    </div>

    <script src="{{ asset('optilux/js/plugins.js') }}"></script>
    <script src="{{ asset('optilux/js/designesia.js') }}"></script>
    @stack('scripts')
</body>
</html>
