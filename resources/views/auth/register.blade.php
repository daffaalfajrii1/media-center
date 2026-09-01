@extends('layouts.auth')

@section('title', 'Daftar Akun')
@section('heading', 'Daftar Akun')
@section('subheading', 'Buat Akun Admin Baru')
@section('description', 'Lengkapi data berikut untuk membuat akun pengelola website.')
@section('content')
    <form method="POST" action="{{ route('register') }}" class="kecamatan-auth-form">
        @csrf

        <div class="mb-4">
            <label for="name" class="kecamatan-auth-form__label">Nama Lengkap</label>
            <input
                id="name"
                type="text"
                name="name"
                class="form-control kecamatan-auth-form__input @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                placeholder="Nama lengkap"
                required
                autofocus
                autocomplete="name"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="kecamatan-auth-form__label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-control kecamatan-auth-form__input @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                placeholder="nama@email.go.id"
                required
                autocomplete="username"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="kecamatan-auth-form__label">Kata Sandi</label>
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
                class="form-control kecamatan-auth-form__input"
                placeholder="Ulangi kata sandi"
                required
                autocomplete="new-password"
            >
        </div>

        <button type="submit" class="btn kecamatan-auth-form__submit w-100">Daftar</button>
    </form>
@endsection

@section('footer_links')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span class="kecamatan-auth-card__meta">Sudah punya akun? <a href="{{ route('login') }}" class="kecamatan-auth-form__link fw-semibold">Masuk</a></span>
        <a href="{{ route('home') }}" class="kecamatan-auth-card__meta">Website publik</a>
    </div>
@endsection
