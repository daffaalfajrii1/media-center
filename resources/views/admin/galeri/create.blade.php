@extends('admin.layouts.app')

@section('title', 'Tambah Album Galeri')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Album Galeri',
        'crumbs' => [
            ['label' => 'Galeri', 'url' => route('admin.galeri.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.galeri.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('admin.galeri._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan & Kelola Media</button>
                        <a href="{{ route('admin.galeri.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
