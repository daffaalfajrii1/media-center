@php $duralux = asset('duralux/assets'); @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') — {{ $websiteSettings->siteName() }}</title>
    <link rel="icon" href="{{ $websiteSettings->faviconUrl() ?: $duralux.'/images/favicon.ico' }}" type="image/png">
    <link rel="stylesheet" href="{{ $duralux }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ $duralux }}/vendors/css/vendors.min.css">
    <link rel="stylesheet" href="{{ $duralux }}/css/theme.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-kecamatan.css') }}">
</head>
<body class="kecamatan-auth-body">
    <main class="kecamatan-auth-page">
        <div class="kecamatan-auth-page__glow" aria-hidden="true"></div>

        <div class="kecamatan-auth-card">
            @include('auth.partials.brand')

            <div class="kecamatan-auth-card__header">
                <h1 class="kecamatan-auth-card__title">@yield('heading', 'Masuk')</h1>
                @hasSection('subheading')
                    <p class="kecamatan-auth-card__subtitle">@yield('subheading')</p>
                @endif
                @hasSection('description')
                    <p class="kecamatan-auth-card__desc">@yield('description')</p>
                @endif
            </div>

            @if (session('status'))
                <div class="alert alert-success fs-12 mb-0">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger fs-12 mb-0">{{ session('error') }}</div>
            @endif

            <div class="kecamatan-auth-card__body">
                @yield('content')
            </div>

            @hasSection('footer_links')
                <div class="kecamatan-auth-card__footer">
                    @yield('footer_links')
                </div>
            @endif
        </div>

        <p class="kecamatan-auth-page__site">{{ $websiteSettings->siteName() }}</p>
    </main>
</body>
</html>
