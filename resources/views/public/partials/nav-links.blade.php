<ul class="navbar-nav ms-auto kecamatan-public-nav">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
    </li>

    <li class="nav-item dropdown">
        <a
            class="nav-link dropdown-toggle {{ request()->routeIs('public.profil.*') ? 'active' : '' }}"
            href="#"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            Profil
        </a>
        <ul class="dropdown-menu kecamatan-profil-dropdown">
            <li class="dropdown-header">Profil</li>
            @forelse($profilPages as $profilPage)
                <li>
                    <a
                        class="dropdown-item {{ request()->routeIs('public.profil.show') && request()->route()->parameter('slug') === $profilPage->slug ? 'active' : '' }}"
                        href="{{ route('public.profil.show', $profilPage->slug) }}"
                    >
                        {{ $profilPage->title }}
                    </a>
                </li>
            @empty
                <li><span class="dropdown-item-text text-muted">Belum ada halaman</span></li>
            @endforelse
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('public.pegawai.*') ? 'active' : '' }}" href="{{ route('public.pegawai.index') }}">Pegawai</a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#">Berita</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Pengumuman</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('login') }}">Login Admin</a>
    </li>
</ul>
