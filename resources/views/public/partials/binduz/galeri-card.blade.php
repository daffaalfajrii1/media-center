@php
    $thumbnail = $galeri->thumbnailUrl();
    $countLabel = $galeri->itemsCountLabel();
@endphp
<article class="binduz-er-galeri-card-item h-100">
    <div class="binduz-er-galeri-card-item__thumb">
        <a href="{{ route('public.galeri.show', $galeri->slug) }}">
            @if ($thumbnail)
                <img src="{{ $thumbnail }}" alt="{{ $galeri->title }}">
            @else
                <div class="binduz-er-galeri-card-item__placeholder" aria-hidden="true">
                    <i class="fal fa-images"></i>
                </div>
            @endif
            @if ($countLabel !== 'Belum ada media')
                <span class="binduz-er-galeri-card-item__badge">{{ $countLabel }}</span>
            @endif
        </a>
    </div>
    <div class="binduz-er-galeri-card-item__body">
        @if ($galeri->event_date)
            <div class="binduz-er-galeri-card-item__date">
                <i class="fal fa-calendar-alt"></i> {{ $galeri->event_date->translatedFormat('d F Y') }}
            </div>
        @endif
        <h4 class="binduz-er-galeri-card-item__title">
            <a href="{{ route('public.galeri.show', $galeri->slug) }}" title="{{ $galeri->title }}">{{ $galeri->title }}</a>
        </h4>
        @if (! empty($showDescription) && $galeri->description)
            <p class="binduz-er-galeri-card-item__excerpt">{{ \Illuminate\Support\Str::limit($galeri->description, 90) }}</p>
        @endif
    </div>
</article>
