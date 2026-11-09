@extends('admin.layouts.app')

@section('title', 'Token API')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Token API',
        'crumbs' => [
            ['label' => 'Sistem'],
            ['label' => 'Token API'],
        ],
    ])

    <div class="main-content">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="mb-1">Buat Token Baru</h5>
                        <p class="text-muted fs-13 mb-3">
                            Token dipakai WordPress (atau aplikasi lain) untuk mengambil berita dari Media Center via Sanctum.
                        </p>

                        <form method="POST" action="{{ route('admin.api-tokens.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="fw-semibold" for="token-name">Nama Token *</label>
                                <input
                                    type="text"
                                    id="token-name"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', 'wordpress') }}"
                                    placeholder="contoh: wordpress-portal"
                                    maxlength="100"
                                    required
                                >
                                <div class="form-text">Berikan nama yang jelas, misalnya <code>wordpress</code>.</div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-key me-1"></i> Generate Token
                            </button>
                        </form>

                        @if (session('api_token_plain'))
                            <div class="alert alert-warning mt-4 mb-0" id="api-token-once">
                                <div class="fw-semibold mb-1">Token siap dipakai (tampil sekali)</div>
                                <div class="fs-12 text-muted mb-2">
                                    Nama: <strong>{{ session('api_token_name') }}</strong>. Salin dan simpan di <code>wp-config.php</code>.
                                </div>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control font-monospace"
                                        id="api-token-value"
                                        value="{{ session('api_token_plain') }}"
                                        readonly
                                    >
                                    <button type="button" class="btn btn-dark" id="copy-api-token">
                                        <i class="feather-copy me-1"></i> Salin
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="mb-1">Cara Pakai di WordPress</h5>
                        <p class="text-muted fs-13 mb-3">Base URL API:</p>
                        <code class="d-block p-2 bg-light rounded mb-3 user-select-all">{{ $apiBaseUrl }}</code>

                        <ul class="fs-13 text-muted mb-3 ps-3">
                            <li class="mb-1"><code>GET {{ $apiBaseUrl }}/berita/terbaru?limit=4</code></li>
                            <li class="mb-1"><code>GET {{ $apiBaseUrl }}/berita</code></li>
                            <li class="mb-1"><code>GET {{ $apiBaseUrl }}/berita/{slug}</code></li>
                        </ul>

                        <p class="fs-13 mb-1 fw-semibold">Header wajib:</p>
                        <pre class="bg-light rounded p-3 fs-12 mb-0">Authorization: Bearer TOKEN_ANDA
Accept: application/json</pre>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card kecamatan-table-card">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0">Daftar Token</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:50px">No</th>
                                        <th>Nama</th>
                                        <th>Pemilik</th>
                                        <th>Terakhir dipakai</th>
                                        <th>Dibuat</th>
                                        <th class="text-end" style="min-width:100px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tokens as $token)
                                        <tr>
                                            <td>{{ $tokens->firstItem() + $loop->index }}</td>
                                            <td class="fw-semibold">{{ $token->name }}</td>
                                            <td>
                                                @if ($token->tokenable)
                                                    <div>{{ $token->tokenable->name }}</div>
                                                    <div class="fs-12 text-muted">{{ $token->tokenable->email }}</div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $token->last_used_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? 'Belum pernah' }}
                                            </td>
                                            <td>{{ $token->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                            <td class="text-end">
                                                <form
                                                    action="{{ route('admin.api-tokens.destroy', $token) }}"
                                                    method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Cabut token ini? WordPress yang memakai token ini akan gagal mengakses API.')"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Cabut token">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Belum ada token. Buat token baru di formulir sebelah kiri.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($tokens->hasPages())
                            <div class="p-3">{{ $tokens->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('copy-api-token');
    const input = document.getElementById('api-token-value');
    if (!btn || !input) return;

    btn.addEventListener('click', async function () {
        try {
            await navigator.clipboard.writeText(input.value);
            btn.innerHTML = '<i class="feather-check me-1"></i> Tersalin';
            setTimeout(function () {
                btn.innerHTML = '<i class="feather-copy me-1"></i> Salin';
            }, 2000);
        } catch (e) {
            input.select();
            document.execCommand('copy');
        }
    });
});
</script>
@endpush
