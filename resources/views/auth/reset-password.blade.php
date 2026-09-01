@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi')
@section('heading', 'Atur Ulang Kata Sandi')
@section('subheading', 'Buat Kata Sandi Baru')
@section('description', 'Masukkan kata sandi baru untuk akun admin Anda. Pastikan kata sandi cukup kuat dan mudah diingat.')
@section('content')
    <form method="POST" action="{{ route('password.store') }}" class="kecamatan-auth-form">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-4">
            <label for="email" class="kecamatan-auth-form__label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-control kecamatan-auth-form__input @error('email') is-invalid @enderror"
                value="{{ old('email', $request->email) }}"
                placeholder="nama@email.go.id"
                required
                autofocus
                autocomplete="username"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="kecamatan-auth-form__label">Kata Sandi Baru</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control kecamatan-auth-form__input @error('password') is-invalid @enderror"
                placeholder="Minimal 8 karakter"
                required
                autocomplete="new-password"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="kecamatan-auth-form__label">Konfirmasi Kata Sandi</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-control kecamatan-auth-form__input @error('password_confirmation') is-invalid @enderror"
                placeholder="Ulangi kata sandi baru"
                required
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn kecamatan-auth-form__submit w-100">Simpan Kata Sandi</button>
    </form>
@endsection

@section('footer_links')
    <a href="{{ route('login') }}" class="kecamatan-auth-card__back">← Kembali ke halaman masuk</a>
@endsection
