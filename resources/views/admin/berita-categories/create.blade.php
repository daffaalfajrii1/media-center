@extends('admin.layouts.app')

@section('title', 'Tambah Kategori Berita')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Kategori',
        'crumbs' => [
            ['label' => 'Berita', 'url' => route('admin.berita.index')],
            ['label' => 'Kategori', 'url' => route('admin.berita-kategori.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.berita-kategori.store') }}">
                    @csrf
                    @include('admin.berita-categories._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.berita-kategori.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
