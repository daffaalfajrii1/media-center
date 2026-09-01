@php
    $terkiniItems = ($beritas ?? collect())->take($limit ?? 3);
@endphp
@if ($terkiniItems->isNotEmpty())
    <div class="binduz-er-terkini-widget {{ $class ?? '' }}">
        <div class="binduz-er-terkini-widget__header">
            <span class="binduz-er-terkini-widget__icon" aria-hidden="true">
                <i class="fal fa-newspaper"></i>
            </span>
            <div class="binduz-er-terkini-widget__intro">
                <h4 class="binduz-er-terkini-widget__title">Berita Terkini</h4>
                <p class="binduz-er-terkini-widget__subtitle">Informasi terbaru dari {{ $websiteSettings->siteName() }}</p>
            </div>
        </div>
        <ul class="binduz-er-terkini-widget__list">
            @foreach ($terkiniItems as $item)
                <li class="binduz-er-terkini-widget__item">
                    <a href="{{ route('public.berita.show', $item->slug) }}" class="binduz-er-terkini-widget__link" title="{{ $item->title }}">
                        {{ $item->title }}
                    </a>
                    @if ($item->published_at)
                        <span class="binduz-er-terkini-widget__date">
                            <i class="fal fa-calendar-alt"></i> {{ $item->published_at->translatedFormat('d M Y') }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
        <a class="binduz-er-terkini-widget__cta binduz-er-main-btn" href="{{ route('public.berita.index') }}">
            Lihat Semua Berita
        </a>
    </div>
@endif
