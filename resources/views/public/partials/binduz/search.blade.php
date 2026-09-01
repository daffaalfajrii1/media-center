@php $logoUrl = $websiteSettings->logoUrl() ?? asset('binduz/assets/images/logo-4.png'); @endphp
<div class="binduz-er-news-search-box">
    <div class="binduz-er-news-search-header">
        <div class="container mt-60">
            <div class="row">
                <div class="col-6">
                    <img src="{{ $logoUrl }}" alt="{{ $websiteSettings->siteName() }}">
                </div>
                <div class="col-6">
                    <div class="binduz-er-news-search-close float-end">
                        <button class="binduz-er-news-search-close-btn" type="button">Tutup <span></span><span></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="binduz-er-news-search-body">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="binduz-er-news-search-form">
                        <form action="{{ route('public.berita.index') }}" method="get">
                            <input type="text" name="search" placeholder="Cari berita..." value="{{ request('search') }}">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
