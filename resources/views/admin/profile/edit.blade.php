@extends('admin.layouts.app')

@section('title', 'Profil Saya')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Profil Saya',
        'crumbs' => [['label' => 'Profil']],
    ])

    <div class="main-content">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <div class="kecamatan-profile-avatar-wrap mx-auto mb-3">
                            @include('admin.partials.user-avatar', ['user' => $user, 'size' => 'lg', 'id' => 'profile-avatar-preview'])
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <div class="text-muted fs-13 mb-3">{{ $user->email }}</div>
                        @if ($user->roles->isNotEmpty())
                            <span class="badge bg-soft-primary text-primary">{{ $user->getRoleNames()->join(', ') }}</span>
                        @endif
                        <div class="mt-3 text-muted fs-12">
                            Bergabung {{ $user->created_at?->format('d F Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="feather-user me-2 text-primary"></i>Informasi Profil</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="fw-semibold">Foto Profil</label>
                                    <input type="file" name="avatar" id="avatar-input" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                                    <div class="form-text">JPG, PNG, GIF, atau WebP. Maks. 2 MB. Tampil di header admin.</div>
                                    @error('avatar')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                    @if ($user->avatarUrl())
                                        <div class="form-check mt-2">
                                            <input type="checkbox" name="remove_avatar" value="1" class="form-check-input" id="remove-avatar">
                                            <label class="form-check-label" for="remove-avatar">Hapus foto profil</label>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Simpan Profil</button>
                                @if (session('status') === 'profile-updated')
                                    <span class="text-success fs-13 ms-2">Berhasil disimpan.</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="feather-lock me-2 text-warning"></i>Ubah Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="fw-semibold">Password Saat Ini</label>
                                    <input type="password" name="current_password" class="form-control" autocomplete="current-password">
                                    @error('current_password', 'updatePassword')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Password Baru</label>
                                    <input type="password" name="password" class="form-control" autocomplete="new-password">
                                    @error('password', 'updatePassword')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                                    @error('password_confirmation', 'updatePassword')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-warning text-dark">Ubah Password</button>
                                @if (session('status') === 'password-updated')
                                    <span class="text-success fs-13 ms-2">Password berhasil diubah.</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('avatar-input')?.addEventListener('change', function (e) {
    const file = e.target.files?.[0];
    if (!file) return;
    const preview = document.getElementById('profile-avatar-preview');
    if (!preview) return;
    const url = URL.createObjectURL(file);
    if (preview.tagName === 'IMG') {
        preview.src = url;
    } else {
        const img = document.createElement('img');
        img.id = 'profile-avatar-preview';
        img.src = url;
        img.alt = 'Preview';
        img.className = 'kecamatan-user-avatar kecamatan-user-avatar--photo kecamatan-user-avatar--lg';
        preview.replaceWith(img);
    }
});
</script>
@endpush
