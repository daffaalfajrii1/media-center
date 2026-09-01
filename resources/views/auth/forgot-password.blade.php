@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')
@section('heading', 'Lupa Kata Sandi')
@section('subheading', 'Reset Kata Sandi Admin')
@section('description', 'Masukkan email akun admin Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.')
@section('content')
    <form method="POST" action="{{ route('password.email') }}" class="kecamatan-auth-form">
        @csrf

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
                autofocus
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn kecamatan-auth-form__submit w-100">Kirim Tautan Reset</button>
    </form>
@endsection

@section('footer_links')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <a href="{{ route('login') }}" class="kecamatan-auth-card__back">← Kembali ke halaman masuk</a>
        <a href="{{ route('home') }}" class="kecamatan-auth-card__meta">Website publik</a>
    </div>
@endsection
