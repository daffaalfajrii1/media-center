@extends('admin.layouts.app')

@section('title', 'Kelola Album Galeri')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Kelola Album',
        'crumbs' => [
            ['label' => 'Galeri', 'url' => route('admin.galeri.index')],
            ['label' => 'Kelola'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.galeri.update', $galeri) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.galeri._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Info Album</button>
                        <a href="{{ route('admin.galeri.index') }}" class="btn btn-light">Kembali</a>
                    </div>
                </form>

                @include('admin.galeri._media-manager')
            </div>
        </div>
    </div>
@endsection
