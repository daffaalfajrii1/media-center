<div class="kecamatan-galeri-media mt-4 pt-4 border-top">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h5 class="mb-1">Foto & Video Album</h5>
            <p class="text-muted mb-0 fs-13">Unggah banyak foto sekaligus atau tambahkan video dari YouTube.</p>
        </div>
        <span class="badge bg-soft-primary text-primary px-3 py-2">
            {{ $galeri->items->count() }} media
        </span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border h-100">
                <div class="card-body">
                    <h6 class="mb-3"><i class="feather-image me-2 text-primary"></i>Tambah Foto</h6>
                    <form method="POST" action="{{ route('admin.galeri.foto.store', $galeri) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="photos[]" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" multiple required>
                            <div class="form-text">Pilih satu atau banyak foto (maks. 5 MB per file).</div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="feather-upload me-1"></i> Unggah Foto
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border h-100">
                <div class="card-body">
                    <h6 class="mb-3"><i class="feather-youtube me-2 text-danger"></i>Tambah Video YouTube</h6>
                    <form method="POST" action="{{ route('admin.galeri.youtube.store', $galeri) }}">
                        @csrf
                        <div class="mb-2">
                            <input type="url" name="youtube_url" class="form-control" value="{{ old('youtube_url') }}" placeholder="https://www.youtube.com/watch?v=..." required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Judul video (opsional)">
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="feather-plus me-1"></i> Tambah Video
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($galeri->items->isEmpty())
        <div class="kecamatan-galeri-empty text-center py-5">
            <i class="feather-image fs-1 text-muted mb-3 d-block"></i>
            <p class="text-muted mb-0">Belum ada foto atau video. Mulai unggah media di atas.</p>
        </div>
    @else
        <div class="row g-3 kecamatan-galeri-media-grid">
            @foreach ($galeri->items as $media)
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="kecamatan-galeri-media-card card h-100 border-0 shadow-sm">
                        <div class="kecamatan-galeri-media-card__thumb">
                            @if ($media->isPhoto())
                                <img src="{{ $media->thumbnailUrl() }}" alt="{{ $media->title ?? 'Foto' }}" loading="lazy">
                            @else
                                <img src="{{ $media->thumbnailUrl() }}" alt="{{ $media->title ?? 'Video YouTube' }}" loading="lazy">
                                <span class="kecamatan-galeri-media-card__play">
                                    <i class="feather-play"></i>
                                </span>
                            @endif
                            <span class="kecamatan-galeri-media-card__badge {{ $media->isPhoto() ? 'is-photo' : 'is-video' }}">
                                {{ $media->isPhoto() ? 'Foto' : 'YouTube' }}
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <div class="fw-semibold text-truncate" title="{{ $media->title }}">
                                {{ $media->title ?: ($media->isPhoto() ? 'Foto #' . $loop->iteration : 'Video YouTube') }}
                            </div>
                            @if ($media->isYoutube())
                                <a href="{{ $media->youtube_url }}" target="_blank" rel="noopener" class="fs-12 text-muted text-truncate d-block">
                                    {{ $media->youtube_url }}
                                </a>
                            @endif
                            <form action="{{ route('admin.galeri.media.destroy', [$galeri, $media]) }}" method="POST" class="mt-2" onsubmit="return confirm('Hapus media ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger w-100">
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
