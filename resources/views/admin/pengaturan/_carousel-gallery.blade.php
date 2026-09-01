<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Upload & Daftar Gambar</h6>
        @if ($carousels->isNotEmpty())
            <span class="badge bg-soft-info text-info">{{ $carousels->count() }} gambar</span>
        @endif
    </div>
    <div class="card-body">
        @if ($errors->has('images') || $errors->has('images.*'))
            <div class="alert alert-danger fs-13">
                {{ $errors->first('images') ?: $errors->first('images.*') }}
            </div>
        @endif

        @if ($carousels->isEmpty())
            <div class="kecamatan-carousel-admin-empty text-muted fs-13 mb-3">
                Belum ada gambar carousel. Beranda akan menampilkan banner default sampai Anda upload gambar baru.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pengaturan.carousel.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="fw-semibold" for="carousel-images">Pilih Gambar *</label>
                <input type="file"
                       id="carousel-images"
                       name="images[]"
                       class="form-control kecamatan-file-input"
                       accept="image/jpeg,image/png,image/gif,image/webp"
                       multiple
                       required>
                <div class="form-text">Bisa pilih banyak gambar sekaligus. Klik tombol <strong>Upload Carousel</strong> (bukan Simpan Pengaturan).</div>
            </div>
            <div class="mb-3">
                <label class="fw-semibold">Judul Slide (opsional)</label>
                <input type="text" name="titles[]" class="form-control" placeholder="Judul untuk gambar pertama">
            </div>
            <button type="submit" class="btn btn-info text-white">
                <i class="feather-upload me-1"></i> Upload Carousel
            </button>
        </form>

        @if ($carousels->isNotEmpty())
            <hr class="my-4">
            <h6 class="fw-semibold mb-3">Gambar Terupload</h6>
            <div class="row g-3">
                @foreach ($carousels as $carousel)
                    <div class="col-md-6 col-xl-4">
                        <div class="kecamatan-carousel-admin-item">
                            <img src="{{ $carousel->imageUrl() }}" alt="{{ $carousel->title ?: 'Carousel' }}" class="kecamatan-carousel-admin-item__img">
                            <div class="kecamatan-carousel-admin-item__body">
                                @if ($carousel->title)
                                    <div class="fw-medium fs-13 text-truncate">{{ $carousel->title }}</div>
                                @endif
                                @if ($carousel->caption)
                                    <div class="text-muted fs-12 text-truncate">{{ $carousel->caption }}</div>
                                @endif
                                <form method="POST" action="{{ route('admin.pengaturan.carousel.destroy', $carousel) }}" class="mt-2" onsubmit="return confirm('Hapus gambar carousel ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="feather-trash-2 me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
