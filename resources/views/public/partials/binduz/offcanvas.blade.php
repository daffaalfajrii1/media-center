<div class="binduz-er-news-off_canvars_overlay"></div>
<div class="binduz-er-news-offcanvas_menu">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="binduz-er-news-offcanvas_menu_wrapper">
                    <div class="binduz-er-news-canvas_close">
                        <a href="javascript:void(0)" aria-label="Tutup menu"><i class="fal fa-times"></i></a>
                    </div>

                    <div class="binduz-er-offcanvas-brand text-center">
                        @include('public.partials.binduz.site-logo', [
                            'variant' => 'offcanvas',
                            'linkClass' => 'd-inline-flex align-items-center justify-content-center',
                        ])
                    </div>

                    <div id="menu" class="text-left">
                        <ul class="binduz-er-news-offcanvas_main_menu">
                            <li class="{{ request()->routeIs('home') ? 'binduz-er-news-active' : '' }}">
                                <a href="{{ route('home') }}">Beranda</a>
                            </li>
                            @if ($profilPages->isNotEmpty())
                                <li class="binduz-er-news-menu-item-has-children">
                                    <a href="#">Profil</a>
                                    <ul class="binduz-er-news-sub-menu">
                                        @foreach ($profilPages as $profilPage)
                                            <li><a href="{{ route('public.profil.show', $profilPage->slug) }}">{{ $profilPage->title }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                            <li><a href="{{ route('public.berita.index') }}">Berita</a></li>
                            <li><a href="{{ route('public.agenda.index') }}">Agenda</a></li>
                            <li><a href="{{ route('public.galeri.index') }}">Galeri</a></li>
                            <li><a href="{{ route('public.pengumuman.index') }}">Pengumuman</a></li>
                            <li class="binduz-er-news-menu-item-has-children">
                                <a href="{{ route('public.layanan.index') }}">Layanan</a>
                                <ul class="binduz-er-news-sub-menu">
                                    <li><a href="{{ route('public.layanan.index') }}">Daftar Layanan</a></li>
                                    <li><a href="{{ route('public.layanan.check-status') }}">Cek Status</a></li>
                                </ul>
                            </li>
                            <li><a href="{{ route('public.pegawai.index') }}">Keanggotaan</a></li>
                            <li><a href="{{ route('public.kontak.index') }}">Kontak</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
