@php
    $marqueeItems = $websiteSettings->marqueeItems();
@endphp

@if (count($marqueeItems) > 0)
    <div class="kecamatan-marquee">
        <div class="kecamatan-marquee__track">
            @foreach (array_merge($marqueeItems, $marqueeItems) as $item)
                <span class="kecamatan-marquee__item">{{ $item }}</span>
            @endforeach
        </div>
    </div>
@endif
