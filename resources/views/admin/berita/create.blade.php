@extends('admin.layouts.app')

@section('title', 'Tambah Berita')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Berita',
        'crumbs' => [
            ['label' => 'Berita', 'url' => route('admin.berita.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form id="berita-form" method="POST" action="{{ route('admin.berita.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('admin.berita._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.berita.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
