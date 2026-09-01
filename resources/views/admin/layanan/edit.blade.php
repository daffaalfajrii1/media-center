@extends('admin.layouts.app')

@section('title', 'Atur Layanan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Atur Layanan',
        'crumbs' => [
            ['label' => 'Layanan', 'url' => route('admin.layanan.index')],
            ['label' => 'Atur'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.layanan.update', $layanan) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.layanan._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Info Layanan</button>
                        <a href="{{ route('admin.layanan.index') }}" class="btn btn-light">Kembali</a>
                    </div>
                </form>

                @include('admin.layanan._field-builder')
            </div>
        </div>
    </div>
@endsection
