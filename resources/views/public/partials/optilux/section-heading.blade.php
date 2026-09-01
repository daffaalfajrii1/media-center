<div class="row mb-4 g-3 align-items-end justify-content-between kecamatan-section-heading">
    <div class="col-lg-8">
        @if (!empty($subtitle))
            <div class="subtitle wow fadeInUp">{{ $subtitle }}</div>
        @endif
        <h2 class="kecamatan-section-title mb-0">{{ $title }}</h2>
    </div>
    @if (!empty($buttonUrl) && !empty($buttonLabel))
        <div class="col-lg-4 text-lg-end">
            <a href="{{ $buttonUrl }}" class="btn-main fx-slide"><span>{{ $buttonLabel }}</span></a>
        </div>
    @endif
</div>
