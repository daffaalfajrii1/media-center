<div class="kecamatan-pengumuman-files mt-4 pt-4 border-top">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h5 class="mb-1">File Unduhan (PDF)</h5>
            <p class="text-muted mb-0 fs-13">Lampirkan surat edaran, formulir, atau dokumen PDF yang bisa diunduh masyarakat.</p>
        </div>
        <span class="badge bg-soft-warning text-warning px-3 py-2">
            {{ $pengumuman->attachments->count() }} file
        </span>
    </div>

    <div class="card border mb-4">
        <div class="card-body">
            <h6 class="mb-3"><i class="feather-upload me-2 text-warning"></i>Unggah File PDF</h6>
            <form method="POST" action="{{ route('admin.pengumuman.lampiran.store', $pengumuman) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" name="files[]" class="form-control" accept="application/pdf,.pdf" multiple required>
                    <div class="form-text">Format PDF saja. Maks. 10 MB per file. Bisa pilih banyak sekaligus.</div>
                </div>
                <button type="submit" class="btn btn-warning btn-sm text-dark">
                    <i class="feather-upload me-1"></i> Unggah PDF
                </button>
            </form>
        </div>
    </div>

    @if ($pengumuman->attachments->isEmpty())
        <div class="kecamatan-pengumuman-files-empty text-center py-5">
            <i class="feather-file-text fs-1 text-muted mb-3 d-block"></i>
            <p class="text-muted mb-0">Belum ada file PDF. Unggah dokumen unduhan di atas.</p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($pengumuman->attachments as $attachment)
                <div class="col-md-6 col-xl-4">
                    <div class="kecamatan-pdf-card card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex gap-3">
                            <div class="kecamatan-pdf-card__icon flex-shrink-0">
                                <i class="feather-file-text"></i>
                                <span>PDF</span>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-semibold text-truncate" title="{{ $attachment->displayTitle() }}">
                                    {{ $attachment->displayTitle() }}
                                </div>
                                <div class="text-muted fs-12 text-truncate" title="{{ $attachment->original_name }}">
                                    {{ $attachment->original_name }}
                                </div>
                                <div class="text-muted fs-12 mt-1">{{ $attachment->formattedFileSize() }}</div>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ $attachment->downloadUrl() }}" target="_blank" class="btn btn-sm btn-light-brand">
                                        <i class="feather-download me-1"></i> Lihat
                                    </a>
                                    <form action="{{ route('admin.pengumuman.lampiran.destroy', [$pengumuman, $attachment]) }}" method="POST" onsubmit="return confirm('Hapus file PDF ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
