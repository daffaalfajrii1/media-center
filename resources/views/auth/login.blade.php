@extends('layouts.auth')

@section('title', 'Masuk Admin')
@section('heading', 'Masuk Admin')
@section('subheading', 'Panel Pengelolaan Website')
@section('description', 'Gunakan akun admin untuk mengelola konten, layanan, dan pengaturan website kecamatan.')

@section('content')
    <form method="POST" action="{{ route('login') }}" class="kecamatan-auth-form">
        @csrf

        <div class="mb-3">
            <label for="email" class="kecamatan-auth-form__label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-control kecamatan-auth-form__input @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                placeholder="nama@email.go.id"
                required
                autofocus
                autocomplete="username"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="kecamatan-auth-form__label">Kata Sandi</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control kecamatan-auth-form__input @error('password') is-invalid @enderror"
                placeholder="Masukkan kata sandi"
                required
                autocomplete="current-password"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div class="form-check mb-0">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                <label class="form-check-label fs-12 c-pointer" for="remember_me">Ingat saya</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="kecamatan-auth-form__link">Lupa kata sandi?</a>
            @endif
        </div>

        <button type="submit" class="btn kecamatan-auth-form__submit w-100">Masuk</button>
    </form>
@endsection

@section('footer_links')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <a href="{{ route('home') }}" class="kecamatan-auth-card__back">← Kembali ke website</a>
        @if (Route::has('register'))
            <span class="kecamatan-auth-card__meta">Belum punya akun? <a href="{{ route('register') }}" class="kecamatan-auth-form__link fw-semibold">Daftar</a></span>
        @endif
    </div>
@endsection
