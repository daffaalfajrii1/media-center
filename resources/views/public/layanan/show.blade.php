@extends('public.layouts.binduz')

@section('title', $layanan->title)

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', [
        'items' => [
            ['label' => 'Layanan', 'url' => route('public.layanan.index')],
            ['label' => $layanan->title],
        ],
    ])
@endsection

@section('content')
    <section class="binduz-er-page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 mb-30">
                    @foreach ($allLayanans as $item)
                        <a href="{{ route('public.layanan.show', $item->slug) }}"
                           class="binduz-er-layanan-card d-block mb-2 {{ $item->id === $layanan->id ? 'border border-danger' : '' }}">
                            <h5 class="mb-0">{{ $item->title }}</h5>
                        </a>
                    @endforeach
                    <a href="{{ route('public.layanan.check-status') }}" class="binduz-er-main-btn w-100 mt-2">Cek Status</a>
                </div>
                <div class="col-lg-9">
                    <small class="text-muted">{{ $layanan->code }}</small>
                    <h2 class="mb-3">{{ $layanan->title }}</h2>
                    @if ($layanan->description)
                        <p>{{ $layanan->description }}</p>
                    @endif

                    @if ($layanan->fields->isEmpty())
                        <div class="alert alert-warning">Formulir layanan belum dikonfigurasi. Silakan hubungi kantor.</div>
                    @else
                        <div class="binduz-er-layanan-card">
                            <h4 class="mb-4">Formulir Pengajuan</h4>
                            <form method="post" action="{{ route('public.layanan.store', $layanan->slug) }}" enctype="multipart/form-data">
                                @csrf
                                @foreach ($layanan->fields as $field)
                                    @include('public.partials.binduz.field-input', ['field' => $field])
                                @endforeach
                                <button type="submit" class="binduz-er-main-btn w-100">Kirim Pengajuan</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
