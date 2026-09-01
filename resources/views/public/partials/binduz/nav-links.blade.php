@php
    $isActive = fn (array $routes) => request()->routeIs(...$routes) ? 'active' : '';
@endphp

<ul class="navbar-nav m-auto">
    <li class="nav-item {{ $isActive(['home']) }}">
        <a class="nav-link" href="{{ route('home') }}">Beranda</a>
    </li>
    @if ($profilPages->isNotEmpty())
        <li class="nav-item {{ $isActive(['public.profil.show']) }}">
            <a class="nav-link" href="#">
                <span>Profil</span><i class="fa fa-angle-down binduz-er-nav-caret" aria-hidden="true"></i>
            </a>
            <ul class="sub-menu">
                @foreach ($profilPages as $profilPage)
                    <li><a href="{{ route('public.profil.show', $profilPage->slug) }}">{{ $profilPage->title }}</a></li>
                @endforeach
            </ul>
        </li>
    @endif
    <li class="nav-item {{ $isActive(['public.berita.*']) }}">
        <a class="nav-link" href="{{ route('public.berita.index') }}">Berita</a>
    </li>
    <li class="nav-item {{ $isActive(['public.agenda.*']) }}">
        <a class="nav-link" href="{{ route('public.agenda.index') }}">Agenda</a>
    </li>
    <li class="nav-item {{ $isActive(['public.galeri.*']) }}">
        <a class="nav-link" href="{{ route('public.galeri.index') }}">Galeri</a>
    </li>
    <li class="nav-item {{ $isActive(['public.pengumuman.*']) }}">
        <a class="nav-link" href="{{ route('public.pengumuman.index') }}">Pengumuman</a>
    </li>
    <li class="nav-item {{ $isActive(['public.layanan.*']) }}">
        <a class="nav-link" href="{{ route('public.layanan.index') }}">
            <span>Layanan</span><i class="fa fa-angle-down binduz-er-nav-caret" aria-hidden="true"></i>
        </a>
        <ul class="sub-menu">
            <li><a href="{{ route('public.layanan.index') }}">Daftar Layanan</a></li>
            <li><a href="{{ route('public.layanan.check-status') }}">Cek Status</a></li>
        </ul>
    </li>
    <li class="nav-item {{ $isActive(['public.pegawai.*']) }}">
        <a class="nav-link" href="{{ route('public.pegawai.index') }}">Keanggotaan</a>
    </li>
    <li class="nav-item {{ $isActive(['public.kontak.*']) }}">
        <a class="nav-link" href="{{ route('public.kontak.index') }}">Kontak</a>
    </li>
</ul>
