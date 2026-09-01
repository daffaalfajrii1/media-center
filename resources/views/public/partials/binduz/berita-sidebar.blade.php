@php $fallbackImage = asset('binduz/assets/images/latest-post-1.jpg'); @endphp
<div class="binduz-er-populer-news-sidebar">
    @if (isset($berita))
        @include('public.partials.binduz.berita-author-widget', ['berita' => $berita])
    @endif

    <div class="binduz-er-author-sidebar-search-bar">
        <form action="{{ route('public.berita.index') }}" method="get">
            <div class="binduz-er-input-box">
                <input type="text" name="search" placeholder="Cari berita..." value="{{ request('search') }}">
                <button type="submit"><i class="fal fa-search"></i></button>
            </div>
        </form>
    </div>

    @if (($popularBeritas ?? collect())->isNotEmpty() || ($latestBeritas ?? collect())->isNotEmpty())
        <div class="binduz-er-populer-news-sidebar-post pt-40">
            <div class="binduz-er-popular-news-title">
                <ul class="nav nav-pills mb-3" id="berita-sidebar-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="berita-popular-tab" data-bs-toggle="pill" href="#berita-popular" role="tab">Terpopuler</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="berita-recent-tab" data-bs-toggle="pill" href="#berita-recent" role="tab">Terbaru</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="berita-sidebar-tabContent">
                <div class="tab-pane fade show active" id="berita-popular" role="tabpanel">
                    <div class="binduz-er-sidebar-latest-post-box">
                        @foreach ($popularBeritas ?? [] as $item)
                            <div class="binduz-er-sidebar-latest-post-item">
                                <div class="binduz-er-thumb">
                                    <a href="{{ route('public.berita.show', $item->slug) }}">
                                        <img src="{{ $item->thumbnailUrl() ?? $fallbackImage }}" alt="{{ $item->title }}">
                                    </a>
                                </div>
                                <div class="binduz-er-content">
                                    @if ($item->published_at)
                                        <span><i class="fal fa-calendar-alt"></i> {{ $item->published_at->translatedFormat('d F Y') }}</span>
                                    @endif
                                    <h4 class="binduz-er-title">
                                        <a href="{{ route('public.berita.show', $item->slug) }}">{{ $item->title }}</a>
                                    </h4>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" id="berita-recent" role="tabpanel">
                    <div class="binduz-er-sidebar-latest-post-box">
                        @foreach ($latestBeritas ?? [] as $item)
                            <div class="binduz-er-sidebar-latest-post-item">
                                <div class="binduz-er-thumb">
                                    <a href="{{ route('public.berita.show', $item->slug) }}">
                                        <img src="{{ $item->thumbnailUrl() ?? $fallbackImage }}" alt="{{ $item->title }}">
                                    </a>
                                </div>
                                <div class="binduz-er-content">
                                    @if ($item->published_at)
                                        <span><i class="fal fa-calendar-alt"></i> {{ $item->published_at->translatedFormat('d F Y') }}</span>
                                    @endif
                                    <h4 class="binduz-er-title">
                                        <a href="{{ route('public.berita.show', $item->slug) }}">{{ $item->title }}</a>
                                    </h4>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('public.partials.binduz.berita-terkini-widget', [
        'beritas' => $latestBeritas ?? collect(),
        'limit' => 3,
        'class' => 'mt-40',
    ])
</div>
