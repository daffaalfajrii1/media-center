@extends('public.layouts.binduz')

@section('title', 'Layanan Publik')

@section('breadcrumb')
    @include('public.partials.binduz.breadcrumb', ['items' => [['label' => 'Layanan']]])
@endsection

@section('content')
    <section class="binduz-er-page-content">
        <div class="container">
            <div class="binduz-er-section-title mb-30"><h3>Layanan Publik</h3></div>
            <div class="row">
                @forelse ($layanans as $layanan)
                    <div class="col-lg-4 col-md-6 mb-30">
                        <div class="binduz-er-layanan-card h-100">
                            <small class="text-muted">{{ $layanan->code }}</small>
                            <h4><a href="{{ route('public.layanan.show', $layanan->slug) }}">{{ $layanan->title }}</a></h4>
                            <p class="mb-0">{{ $layanan->description ?: 'Ajukan layanan secara online.' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="binduz-er-layanan-card text-center"><p class="mb-0">Belum ada layanan publik yang aktif.</p></div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @include('public.partials.binduz.layanan-cta')
@endsection
