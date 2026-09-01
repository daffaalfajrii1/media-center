@extends('admin.layouts.app')

@section('title', 'Tambah Layanan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Layanan',
        'crumbs' => [
            ['label' => 'Layanan', 'url' => route('admin.layanan.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.layanan.store') }}">
                    @csrf
                    @include('admin.layanan._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan & Atur Field</button>
                        <a href="{{ route('admin.layanan.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
