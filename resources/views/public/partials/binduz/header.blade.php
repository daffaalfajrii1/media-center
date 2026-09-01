<header class="binduz-er-header-area">
    <div class="binduz-er-header-nav">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="navigation">
                        <nav class="navbar navbar-expand-lg binduz-er-main-navbar">
                            <div class="navbar-brand logo">
                                @include('public.partials.binduz.site-logo')
                            </div>
                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                @include('public.partials.binduz.nav-links')
                            </div>
                            <div class="binduz-er-navbar-btn d-flex">
                                <div class="binduz-er-widget d-flex">
                                    <a class="binduz-er-news-search-open" href="#"><i class="far fa-search"></i></a>
                                    <a href="{{ route('login') }}" title="Login Admin"><i class="far fa-user"></i></a>
                                </div>
                                <span class="binduz-er-toggle-btn binduz-er-news-canvas_open d-block d-lg-none">
                                    <i class="fal fa-bars"></i>
                                </span>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
