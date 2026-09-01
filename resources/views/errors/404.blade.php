@php
    $binduz = asset('binduz/assets');
    $siteName = isset($websiteSettings) ? $websiteSettings->siteName() : config('app.name', 'Media Center');
    $logoUrl = isset($websiteSettings) ? $websiteSettings->logoUrl() : null;
    $favicon = (isset($websiteSettings) ? $websiteSettings->faviconUrl() : null) ?: $binduz.'/images/favicon.ico';
    $tagline = (isset($websiteSettings) && filled($websiteSettings->site_tagline))
        ? $websiteSettings->site_tagline
        : 'Website resmi informasi publik';
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, follow">
    <title>Halaman Tidak Ditemukan — {{ $siteName }}</title>
    <link rel="shortcut icon" href="{{ $favicon }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ $binduz }}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ $binduz }}/css/font-awesome.min.css">
    <style>
        :root {
            --mc-ink: #0f172a;
            --mc-muted: #64748b;
            --mc-blue: #1d4ed8;
            --mc-blue-soft: #2563eb;
            --mc-sky: #eff6ff;
            --mc-line: #e2e8f0;
            --mc-surface: #ffffff;
        }

        * { box-sizing: border-box; }

        body.mc-error-page {
            margin: 0;
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", system-ui, sans-serif;
            color: var(--mc-ink);
            background:
                radial-gradient(ellipse 80% 50% at 10% -10%, rgba(37, 99, 235, 0.12), transparent 55%),
                radial-gradient(ellipse 70% 45% at 100% 0%, rgba(14, 165, 233, 0.1), transparent 50%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 48%, #f8fafc 100%);
            display: flex;
            flex-direction: column;
        }

        .mc-error-page__glow {
            position: fixed;
            inset: auto -10% -20% auto;
            width: min(520px, 70vw);
            height: min(520px, 70vw);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.08), transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .mc-error-shell {
            position: relative;
            z-index: 1;
            width: min(920px, calc(100% - 2rem));
            margin: auto;
            padding: clamp(2rem, 5vw, 3.5rem) 0;
        }

        .mc-error-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--mc-ink);
            margin-bottom: 2rem;
            opacity: 0;
            animation: mc-fade-up 0.6s ease forwards;
        }

        .mc-error-brand__logo {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 10px;
            background: var(--mc-surface);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
            padding: 4px;
        }

        .mc-error-brand__mark {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, var(--mc-blue), #38bdf8);
            color: #fff;
            font-weight: 800;
            font-size: 0.95rem;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }

        .mc-error-brand__text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .mc-error-brand__name {
            font-weight: 800;
            font-size: 1rem;
        }

        .mc-error-brand__tag {
            font-size: 0.75rem;
            color: var(--mc-muted);
            font-weight: 500;
        }

        .mc-error-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 24px;
            padding: clamp(1.75rem, 4vw, 3rem);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            opacity: 0;
            animation: mc-fade-up 0.7s ease 0.12s forwards;
        }

        .mc-error-layout {
            display: grid;
            gap: 1.75rem;
            align-items: center;
        }

        @media (min-width: 768px) {
            .mc-error-layout {
                grid-template-columns: 1.05fr 0.95fr;
                gap: 2.5rem;
            }
        }

        .mc-error-visual {
            position: relative;
            min-height: 220px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            background:
                linear-gradient(160deg, #eff6ff 0%, #dbeafe 55%, #e0f2fe 100%);
            overflow: hidden;
        }

        .mc-error-visual::before,
        .mc-error-visual::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.45);
        }

        .mc-error-visual::before {
            width: 160px;
            height: 160px;
            top: -40px;
            left: -30px;
        }

        .mc-error-visual::after {
            width: 120px;
            height: 120px;
            right: -20px;
            bottom: -30px;
        }

        .mc-error-code {
            position: relative;
            z-index: 1;
            font-size: clamp(5.5rem, 16vw, 8rem);
            font-weight: 800;
            letter-spacing: -0.06em;
            line-height: 0.9;
            background: linear-gradient(180deg, #1e3a8a 0%, #2563eb 55%, #38bdf8 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: mc-float 4.5s ease-in-out infinite;
        }

        .mc-error-compass {
            position: absolute;
            z-index: 2;
            bottom: 1.1rem;
            right: 1.1rem;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
            color: var(--mc-blue-soft);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
            animation: mc-pulse 2.8s ease-in-out infinite;
        }

        .mc-error-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: var(--mc-sky);
            color: var(--mc-blue);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            margin-bottom: 0.9rem;
        }

        .mc-error-title {
            font-size: clamp(1.55rem, 3vw, 2.1rem);
            font-weight: 800;
            line-height: 1.25;
            margin: 0 0 0.75rem;
            color: var(--mc-ink);
        }

        .mc-error-copy {
            margin: 0 0 1.5rem;
            color: var(--mc-muted);
            font-size: 1rem;
            line-height: 1.65;
            max-width: 36ch;
        }

        .mc-error-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.4rem;
        }

        .mc-error-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            min-height: 46px;
            padding: 0.7rem 1.2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .mc-error-btn--primary {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: #fff;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.28);
        }

        .mc-error-btn--primary:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.34);
        }

        .mc-error-btn--ghost {
            background: #fff;
            color: #1e293b;
            border: 1px solid var(--mc-line);
        }

        .mc-error-btn--ghost:hover {
            color: var(--mc-blue);
            border-color: #bfdbfe;
            background: #f8fbff;
        }

        .mc-error-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem 1.1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--mc-line);
        }

        .mc-error-links a {
            color: var(--mc-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .mc-error-links a:hover {
            color: var(--mc-blue-soft);
        }

        .mc-error-foot {
            margin-top: 1.5rem;
            text-align: center;
            color: var(--mc-muted);
            font-size: 0.82rem;
            opacity: 0;
            animation: mc-fade-up 0.7s ease 0.28s forwards;
        }

        @keyframes mc-fade-up {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes mc-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes mc-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }

        @media (prefers-reduced-motion: reduce) {
            .mc-error-brand,
            .mc-error-card,
            .mc-error-foot,
            .mc-error-code,
            .mc-error-compass {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body class="mc-error-page">
    <div class="mc-error-page__glow" aria-hidden="true"></div>

    <div class="mc-error-shell">
        <a href="{{ route('home') }}" class="mc-error-brand">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="mc-error-brand__logo">
            @else
                <span class="mc-error-brand__mark" aria-hidden="true">MC</span>
            @endif
            <span class="mc-error-brand__text">
                <span class="mc-error-brand__name">{{ $siteName }}</span>
                <span class="mc-error-brand__tag">{{ $tagline }}</span>
            </span>
        </a>

        <div class="mc-error-card">
            <div class="mc-error-layout">
                <div class="mc-error-visual" aria-hidden="true">
                    <div class="mc-error-code">404</div>
                    <div class="mc-error-compass">
                        <i class="far fa-compass"></i>
                    </div>
                </div>

                <div>
                    <div class="mc-error-kicker">
                        <i class="far fa-exclamation-circle"></i>
                        Halaman tidak ditemukan
                    </div>
                    <h1 class="mc-error-title">Maaf, halaman yang Anda cari tidak tersedia</h1>
                    <p class="mc-error-copy">
                        Tautan mungkin sudah dipindahkan, dihapus, atau salah diketik.
                        Silakan kembali ke beranda atau jelajahi konten lainnya.
                    </p>

                    <div class="mc-error-actions">
                        <a href="{{ route('home') }}" class="mc-error-btn mc-error-btn--primary">
                            <i class="far fa-home"></i>
                            Kembali ke Beranda
                        </a>
                        <a href="javascript:history.back()" class="mc-error-btn mc-error-btn--ghost">
                            <i class="far fa-arrow-left"></i>
                            Halaman Sebelumnya
                        </a>
                    </div>

                    <nav class="mc-error-links" aria-label="Navigasi cepat">
                        <a href="{{ route('public.berita.index') }}">Berita</a>
                        <a href="{{ route('public.agenda.index') }}">Agenda</a>
                        <a href="{{ route('public.galeri.index') }}">Galeri</a>
                        <a href="{{ route('public.pengumuman.index') }}">Pengumuman</a>
                        <a href="{{ route('public.kontak.index') }}">Kontak</a>
                    </nav>
                </div>
            </div>
        </div>

        <p class="mc-error-foot">
            &copy; {{ date('Y') }} {{ $siteName }}. Hak cipta dilindungi.
        </p>
    </div>
</body>
</html>
