@php $marqueeItems = $websiteSettings->marqueeItems(); @endphp
@if ($marqueeItems !== [])
    <div class="binduz-er-marquee" aria-label="Pengumuman">
        <div class="binduz-er-marquee__label">
            <i class="fas fa-bullhorn"></i>
            <span>Pengumuman</span>
        </div>
        <div class="binduz-er-marquee__track-wrap">
            <div class="binduz-er-marquee__track">
                @foreach (array_merge($marqueeItems, $marqueeItems) as $item)
                    <span class="binduz-er-marquee__item">{{ $item }}</span>
                    <span class="binduz-er-marquee__sep" aria-hidden="true">•</span>
                @endforeach
            </div>
        </div>
    </div>
@endif
