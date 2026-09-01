@extends('layouts.auth')

@section('title', 'Konfirmasi Kata Sandi')
@section('heading', 'Konfirmasi Kata Sandi')
@section('subheading', 'Area Aman')
@section('description', 'Untuk melanjutkan, masukkan kata sandi Anda sekali lagi.')
@section('content')
    <form method="POST" action="{{ route('password.confirm') }}" class="kecamatan-auth-form">
        @csrf

        <div class="mb-4">
            <label for="password" class="kecamatan-auth-form__label">Kata Sandi</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control kecamatan-auth-form__input @error('password') is-invalid @enderror"
                placeholder="Masukkan kata sandi"
                required
                autofocus
                autocomplete="current-password"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn kecamatan-auth-form__submit w-100">Konfirmasi</button>
    </form>
@endsection
