@php
    $logoUrl = $websiteSettings->logoUrl() ?? asset('optilux/images/logo-dark.webp');
@endphp

@include('public.partials.optilux.marquee')

<header class="transparent header-light header-float">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="header-inner">
                    <div class="de-flex">
                        <div class="de-flex-col">
                            <div id="logo">
                                <a href="{{ route('home') }}">
                                    <img class="logo-main" src="{{ $logoUrl }}" alt="{{ $websiteSettings->siteName() }}">
                                    <img class="logo-scroll" src="{{ $logoUrl }}" alt="{{ $websiteSettings->siteName() }}">
                                    <img class="logo-mobile" src="{{ $logoUrl }}" alt="{{ $websiteSettings->siteName() }}">
                                </a>
                            </div>
                        </div>

                        <div class="de-flex-col">
                            <div class="de-flex-col header-col-mid">
                                <ul id="mainmenu">
                                    <li>
                                        <a class="menu-item" href="{{ route('home') }}">Beranda</a>
                                    </li>
                                    @if ($profilPages->isNotEmpty())
                                        <li class="has-child">
                                            <a class="menu-item" href="#">Profil</a>
                                            <ul>
                                                @foreach ($profilPages as $profilPage)
                                                    <li>
                                                        <a href="{{ route('public.profil.show', $profilPage->slug) }}">{{ $profilPage->title }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                    <li><a class="menu-item" href="{{ route('public.berita.index') }}">Berita</a></li>
                                    <li><a class="menu-item" href="{{ route('public.agenda.index') }}">Agenda</a></li>
                                    <li><a class="menu-item" href="{{ route('public.galeri.index') }}">Galeri</a></li>
                                    <li><a class="menu-item" href="{{ route('public.pengumuman.index') }}">Pengumuman</a></li>
                                    <li class="has-child">
                                        <a class="menu-item" href="{{ route('public.layanan.index') }}">Layanan</a>
                                        <ul>
                                            <li><a href="{{ route('public.layanan.index') }}">Daftar Layanan</a></li>
                                            <li><a href="{{ route('public.layanan.check-status') }}">Cek Status</a></li>
                                        </ul>
                                    </li>
                                    <li><a class="menu-item" href="{{ route('public.pegawai.index') }}">Pegawai</a></li>
                                    <li><a class="menu-item" href="{{ route('public.kontak.index') }}">Kontak</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="de-flex-col">
                            <a class="btn-main fx-slide w-100" href="{{ route('public.layanan.index') }}"><span>Layanan Online</span></a>
                            <div class="menu_side_area">
                                <span id="menu-btn"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
