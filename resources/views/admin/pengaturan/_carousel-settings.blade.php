<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom">
        <h5 class="mb-0"><i class="feather-sliders me-2 text-info"></i>Carousel Beranda</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-light border fs-13 mb-4">
            <div class="fw-semibold mb-1"><i class="feather-info me-1"></i> Ketentuan ukuran gambar</div>
            <ul class="mb-0 ps-3">
                <li>Ukuran disarankan: <strong>{{ App\Models\WebsiteCarousel::sizeGuideline() }}</strong></li>
                <li>Format: JPG, PNG, atau WebP</li>
                <li>Maks. {{ number_format(App\Models\WebsiteCarousel::MAX_FILE_KB / 1024, 1) }} MB per gambar</li>
                <li>Gunakan foto landscape lebar agar tampil rapi di beranda</li>
            </ul>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="checkbox" name="carousel_enabled" value="1" class="form-check-input" id="carousel-enabled" @checked(old('carousel_enabled', $settings->carousel_enabled))>
                    <label class="form-check-label" for="carousel-enabled">Tampilkan carousel di beranda</label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="fw-semibold">Durasi per slide (detik)</label>
                <input
                    type="number"
                    name="carousel_duration"
                    class="form-control"
                    min="{{ App\Models\WebsiteSetting::CAROUSEL_DURATION_MIN }}"
                    max="{{ App\Models\WebsiteSetting::CAROUSEL_DURATION_MAX }}"
                    value="{{ old('carousel_duration', $settings->carousel_duration ?? App\Models\WebsiteSetting::CAROUSEL_DURATION_DEFAULT) }}"
                    required
                >
                <div class="form-text">
                    {{ App\Models\WebsiteSetting::CAROUSEL_DURATION_MIN }}–{{ App\Models\WebsiteSetting::CAROUSEL_DURATION_MAX }} detik. Default {{ App\Models\WebsiteSetting::CAROUSEL_DURATION_DEFAULT }} detik.
                </div>
                @error('carousel_duration')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
