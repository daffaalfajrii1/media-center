@php $duralux = asset('duralux/assets'); @endphp
<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand kecamatan-panel-brand text-decoration-none">
                <span class="kecamatan-panel-brand__logo-wrap">
                    @if ($websiteSettings->logoUrl())
                        <img src="{{ $websiteSettings->logoUrl() }}" alt="{{ $websiteSettings->siteName() }}" class="logo logo-lg kecamatan-panel-brand__logo" />
                        <img src="{{ $websiteSettings->logoUrl() }}" alt="{{ $websiteSettings->siteName() }}" class="logo logo-sm kecamatan-panel-brand__logo" />
                    @else
                        <img src="{{ $duralux }}/images/logo-full.png" alt="{{ $websiteSettings->siteName() }}" class="logo logo-lg kecamatan-panel-brand__logo" />
                        <img src="{{ $duralux }}/images/logo-abbr.png" alt="{{ $websiteSettings->siteName() }}" class="logo logo-sm kecamatan-panel-brand__logo" />
                    @endif
                </span>
                <span class="kecamatan-panel-brand__name" title="{{ $websiteSettings->siteName() }}">{{ $websiteSettings->siteName() }}</span>
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption"><label>Menu Utama</label></li>

                <li class="nxl-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="nxl-item nxl-caption"><label>Konten Website</label></li>

                @can('manage berita')
                    <li class="nxl-item {{ request()->routeIs('admin.berita.*', 'admin.berita-kategori.*') && ! request()->routeIs('admin.berita.statistik') ? 'active' : '' }}">
                        <a href="{{ route('admin.berita.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file-text"></i></span>
                            <span class="nxl-mtext">Berita</span>
                        </a>
                    </li>
                    <li class="nxl-item {{ request()->routeIs('admin.berita.statistik') ? 'active' : '' }}">
                        <a href="{{ route('admin.berita.statistik') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                            <span class="nxl-mtext">Statistik Berita</span>
                        </a>
                    </li>
                @endcan

                @can('manage agenda')
                    <li class="nxl-item {{ request()->routeIs('admin.agenda.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.agenda.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-calendar"></i></span>
                            <span class="nxl-mtext">Agenda</span>
                        </a>
                    </li>
                @endcan

                @can('manage galeri')
                    <li class="nxl-item {{ request()->routeIs('admin.galeri.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.galeri.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-image"></i></span>
                            <span class="nxl-mtext">Galeri</span>
                        </a>
                    </li>
                @endcan

                @can('manage pengumuman')
                    <li class="nxl-item {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pengumuman.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bell"></i></span>
                            <span class="nxl-mtext">Pengumuman</span>
                        </a>
                    </li>
                @endcan

                @can('manage layanan')
                    <li class="nxl-item {{ request()->routeIs('admin.layanan.*', 'admin.layanan-pengajuan.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.layanan.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-layers"></i></span>
                            <span class="nxl-mtext">Layanan</span>
                        </a>
                    </li>
                @endcan

                @can('manage profil')
                    <li class="nxl-item {{ request()->routeIs('admin.halaman.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.halaman.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-file"></i></span>
                            <span class="nxl-mtext">Halaman Profil</span>
                        </a>
                    </li>
                @endcan

                @can('manage pegawai')
                    <li class="nxl-item {{ request()->routeIs('admin.pegawai.*', 'admin.jabatan.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pegawai.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-user-check"></i></span>
                            <span class="nxl-mtext">Pegawai</span>
                        </a>
                    </li>
                @endcan

                <li class="nxl-item nxl-caption"><label>Sistem</label></li>

                @can('manage users')
                    <li class="nxl-item {{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pengguna.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Pengguna</span>
                        </a>
                    </li>
                @endcan

                @can('manage roles')
                    <li class="nxl-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-shield"></i></span>
                            <span class="nxl-mtext">Role & Permission</span>
                        </a>
                    </li>
                @endcan

                @can('manage pengaturan')
                    <li class="nxl-item {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pengaturan.edit') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">Pengaturan Website</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
</nav>
