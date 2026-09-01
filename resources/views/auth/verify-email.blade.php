@extends('layouts.auth')

@section('title', 'Verifikasi Email')
@section('heading', 'Verifikasi Email')
@section('subheading', 'Konfirmasi Alamat Email')
@section('description', 'Sebelum melanjutkan, silakan verifikasi email Anda dengan mengklik tautan yang kami kirimkan. Jika belum menerima email, Anda dapat meminta pengiriman ulang.')
@section('content')
    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success fs-12 mb-4">
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
        </div>
    @endif

    <div class="d-flex flex-column flex-sm-row align-items-stretch gap-2">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-fill">
            @csrf
            <button type="submit" class="btn kecamatan-auth-form__submit w-100">Kirim Ulang Email Verifikasi</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="flex-fill">
            @csrf
            <button type="submit" class="btn btn-light-brand w-100">Keluar</button>
        </form>
    </div>
@endsection
