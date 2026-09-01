@php $marqueeItems = $websiteSettings->marqueeItems(); @endphp
@if ($marqueeItems !== [])
    <div class="kecamatan-marquee" aria-label="Pengumuman">
        <div class="kecamatan-marquee__label">
            <i class="bi bi-megaphone-fill"></i>
            <span>Pengumuman</span>
        </div>
        <div class="kecamatan-marquee__track-wrap">
            <div class="kecamatan-marquee__track">
                @foreach (array_merge($marqueeItems, $marqueeItems) as $item)
                    <span class="kecamatan-marquee__item">{{ $item }}</span>
                    <span class="kecamatan-marquee__sep">•</span>
                @endforeach
            </div>
        </div>
    </div>
@endif
