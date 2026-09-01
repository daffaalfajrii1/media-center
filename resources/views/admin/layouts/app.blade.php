@php
    $duralux = asset('duralux/assets');
    $appName = $websiteSettings->siteName();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $appName }}</title>
    <link rel="icon" href="{{ $websiteSettings->faviconUrl() ?: $duralux.'/images/favicon.ico' }}" />
    <link rel="stylesheet" href="{{ $duralux }}/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ $duralux }}/vendors/css/vendors.min.css" />
    @stack('styles')
    <link rel="stylesheet" href="{{ $duralux }}/css/theme.min.css" />
    <link rel="stylesheet" href="{{ asset('css/admin-kecamatan.css') }}" />
</head>
<body class="kecamatan-panel-body">
    @include('admin.layouts.partials.sidebar')

    @include('admin.layouts.partials.header')

    <main class="nxl-container kecamatan-panel-container">
        <div class="nxl-content flex-grow-1">
            @include('admin.components.alert')
            @yield('content')
        </div>
        @include('admin.layouts.partials.footer')
    </main>

    @include('admin.layouts.partials.scripts')
    @stack('scripts')
</body>
</html>
