@extends('public.layouts.binduz')



@section('title', 'Keanggotaan')



@section('breadcrumb')

    @include('public.partials.binduz.breadcrumb', ['items' => [['label' => 'Keanggotaan']]])

@endsection



@section('content')

    <section class="binduz-er-page-content">

        <div class="container">

            @if ($totalPegawai > 0)

                <p class="lead mb-5">Daftar keanggotaan {{ $websiteSettings->siteName() }}. Total {{ $totalPegawai }} anggota.</p>

            @endif



            @if ($jabatanGroups->isNotEmpty())

                <div class="row">

                    @foreach ($jabatanGroups as $jabatan)

                        @foreach ($jabatan->pegawais as $pegawai)

                            <div class="col-lg-4 col-md-6 mb-30">

                                <div class="binduz-er-pegawai-card h-100">

                                    <div class="d-flex align-items-center mb-3">

                                        @if ($pegawai->photoUrl())

                                            <img src="{{ $pegawai->photoUrl() }}" alt="{{ $pegawai->name }}" class="me-3">

                                        @else

                                            <div class="binduz-er-pegawai-card rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width:80px;height:80px;font-size:24px;">{{ $pegawai->initials() }}</div>

                                        @endif

                                        <div>

                                            <div class="text-danger small">{{ $jabatan->name }}</div>

                                            <h5 class="mb-0">{{ $pegawai->name }}</h5>

                                            @if ($pegawai->nip)

                                                <small class="text-muted">NIP {{ $pegawai->nip }}</small>

                                            @endif

                                        </div>

                                    </div>

                                    @if ($pegawai->bio)

                                        <p class="small">{{ \Illuminate\Support\Str::limit($pegawai->bio, 100) }}</p>

                                    @endif

                                    <div class="small">

                                        @if ($pegawai->phone)

                                            <div><i class="fal fa-phone"></i> {{ $pegawai->phone }}</div>

                                        @endif

                                        @if ($pegawai->email)

                                            <div><i class="fal fa-envelope"></i> {{ $pegawai->email }}</div>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    @endforeach

                </div>

            @else

                <div class="binduz-er-layanan-card text-center">

                    <p class="mb-0">Belum ada data keanggotaan yang dipublikasikan.</p>

                </div>

            @endif

        </div>

    </section>

@endsection

