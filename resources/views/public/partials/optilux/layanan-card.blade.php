@php
    $icons = ['icofont-file-document', 'icofont-law-document', 'icofont-ui-clip', 'icofont-id-card', 'icofont-certificate', 'icofont-book-mark'];
    $icon = $icons[$index % count($icons)];
@endphp

<div class="kecamatan-layanan-card h-100">
    <a href="{{ route('public.layanan.show', $layanan->slug) }}" class="d-block h-100 text-decoration-none">
        <div class="kecamatan-layanan-card__icon">
            <i class="{{ $icon }}"></i>
        </div>
        <small class="kecamatan-layanan-card__code">{{ $layanan->code }}</small>
        <h4 class="kecamatan-layanan-card__title">{{ $layanan->title }}</h4>
        @if ($layanan->description)
            <p class="kecamatan-layanan-card__desc mb-0">{{ \Illuminate\Support\Str::limit($layanan->description, 100) }}</p>
        @endif
        <span class="kecamatan-layanan-card__link">Ajukan layanan <i class="icofont-rounded-right"></i></span>
    </a>
</div>
