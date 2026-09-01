@extends('admin.layouts.app')

@section('title', 'Pengaturan Website')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Pengaturan Website',
        'crumbs' => [['label' => 'Pengaturan Website']],
    ])

    <div class="main-content">
        <div class="row g-4">
            <div class="col-lg-8">
                <form id="pengaturan-form" method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0"><i class="feather-globe me-2 text-primary"></i>Identitas Website</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="fw-semibold">Nama Website *</label>
                                    <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings->site_name) }}" required>
                                    <div class="form-text">Ditampilkan di header website, admin panel, dan judul halaman.</div>
                                    @error('site_name')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Tagline</label>
                                    <input type="text" name="site_tagline" class="form-control" value="{{ old('site_tagline', $settings->site_tagline) }}">
                                    <div class="form-text">Subjudul singkat di beranda dan footer.</div>
                                    @error('site_tagline')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0"><i class="feather-image me-2 text-primary"></i>Logo & Favicon</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="fw-semibold">Logo Website</label>
                                    <input type="file" name="logo" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                                    <div class="form-text">PNG transparan disarankan. Maks. 2 MB. Dipakai di website publik & panel admin.</div>
                                    @if ($settings->logoUrl())
                                        <div class="mt-3 kecamatan-settings-preview">
                                            <img src="{{ $settings->logoUrl() }}" alt="Logo" class="kecamatan-settings-preview__logo">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="remove_logo" value="1" class="form-check-input" id="remove-logo">
                                                <label class="form-check-label" for="remove-logo">Hapus logo</label>
                                            </div>
                                        </div>
                                    @endif
                                    @error('logo')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Favicon</label>
                                    <input type="file" name="favicon" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp,image/x-icon">
                                    <div class="form-text">Ikon tab browser. Maks. 1 MB. Kosongkan untuk memakai logo.</div>
                                    @if ($settings->favicon_path && $settings->faviconUrl())
                                        <div class="mt-3 kecamatan-settings-preview">
                                            <img src="{{ $settings->faviconUrl() }}" alt="Favicon" class="kecamatan-settings-preview__favicon">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="remove_favicon" value="1" class="form-check-input" id="remove-favicon">
                                                <label class="form-check-label" for="remove-favicon">Hapus favicon</label>
                                            </div>
                                        </div>
                                    @endif
                                    @error('favicon')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('admin.pengaturan._carousel-settings')
                </form>

                @include('admin.pengaturan._carousel-gallery')

                @include('admin.pengaturan._google-analytics-settings')

                <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0"><i class="feather-type me-2 text-warning"></i>Marquee Pengumuman</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="marquee_enabled" value="1" form="pengaturan-form" class="form-check-input" id="marquee-enabled" @checked(old('marquee_enabled', $settings->marquee_enabled))>
                                <label class="form-check-label" for="marquee-enabled">Aktifkan bar pengumuman berjalan</label>
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold">Sumber Teks</label>
                                <select name="marquee_mode" form="pengaturan-form" class="form-select">
                                    <option value="{{ App\Models\WebsiteSetting::MARQUEE_CUSTOM }}" @selected(old('marquee_mode', $settings->marquee_mode) === App\Models\WebsiteSetting::MARQUEE_CUSTOM)>Teks kustom</option>
                                    <option value="{{ App\Models\WebsiteSetting::MARQUEE_PENGUMUMAN }}" @selected(old('marquee_mode', $settings->marquee_mode) === App\Models\WebsiteSetting::MARQUEE_PENGUMUMAN)>Pengumuman terbaru (otomatis)</option>
                                    <option value="{{ App\Models\WebsiteSetting::MARQUEE_OFF }}" @selected(old('marquee_mode', $settings->marquee_mode) === App\Models\WebsiteSetting::MARQUEE_OFF)>Nonaktifkan teks</option>
                                </select>
                            </div>
                            <div>
                                <label class="fw-semibold">Teks Kustom</label>
                                <textarea name="marquee_text" form="pengaturan-form" class="form-control" rows="4" placeholder="Satu baris per pengumuman, atau pisahkan dengan |">{{ old('marquee_text', $settings->marquee_text) }}</textarea>
                                <div class="form-text">Dipakai jika sumber = teks kustom. Pisahkan beberapa pengumuman dengan baris baru.</div>
                                @error('marquee_text')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0"><i class="feather-map-pin me-2 text-danger"></i>Peta Google Maps</h5>
                        </div>
                        <div class="card-body">
                            <label class="fw-semibold">Kode Embed / URL</label>
                            <textarea name="google_maps_embed" form="pengaturan-form" class="form-control" rows="5" placeholder="Tempel URL embed dari Google Maps atau kode &lt;iframe&gt;...">{{ old('google_maps_embed', $settings->google_maps_embed) }}</textarea>
                            <div class="form-text">
                                Buka Google Maps → Share → Embed a map → salin URL <code>src</code> atau seluruh kode iframe.
                            </div>
                            @error('google_maps_embed')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0"><i class="feather-phone me-2 text-success"></i>Kontak & Footer</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="fw-semibold">Alamat</label>
                                    <textarea name="contact_address" form="pengaturan-form" class="form-control" rows="2">{{ old('contact_address', $settings->contact_address) }}</textarea>
                                    @error('contact_address')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold">Telepon</label>
                                    <input type="text" name="contact_phone" form="pengaturan-form" class="form-control" value="{{ old('contact_phone', $settings->contact_phone) }}">
                                    @error('contact_phone')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold">Email</label>
                                    <input type="email" name="contact_email" form="pengaturan-form" class="form-control" value="{{ old('contact_email', $settings->contact_email) }}">
                                    @error('contact_email')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold">WhatsApp</label>
                                    <input type="text" name="contact_whatsapp" form="pengaturan-form" class="form-control" value="{{ old('contact_whatsapp', $settings->contact_whatsapp) }}" placeholder="62812...">
                                    @error('contact_whatsapp')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold">Facebook</label>
                                    <input type="url" name="social_facebook" form="pengaturan-form" class="form-control" value="{{ old('social_facebook', $settings->social_facebook) }}" placeholder="https://facebook.com/...">
                                    @error('social_facebook')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold">Instagram</label>
                                    <input type="url" name="social_instagram" form="pengaturan-form" class="form-control" value="{{ old('social_instagram', $settings->social_instagram) }}" placeholder="https://instagram.com/...">
                                    @error('social_instagram')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold">YouTube</label>
                                    <input type="url" name="social_youtube" form="pengaturan-form" class="form-control" value="{{ old('social_youtube', $settings->social_youtube) }}" placeholder="https://youtube.com/...">
                                    @error('social_youtube')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="fw-semibold">Teks Footer</label>
                                    <textarea name="footer_text" form="pengaturan-form" class="form-control" rows="2">{{ old('footer_text', $settings->footer_text) }}</textarea>
                                    @error('footer_text')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 90px;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Simpan Perubahan</h6>
                        <p class="text-muted fs-13">Untuk pengaturan umum (nama, logo, durasi carousel, dll). Upload gambar carousel pakai tombol biru <strong>Upload Carousel</strong> di bagian carousel.</p>
                        <button type="submit" form="pengaturan-form" class="btn btn-primary w-100">
                            <i class="feather-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
