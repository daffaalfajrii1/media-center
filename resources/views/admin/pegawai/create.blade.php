@extends('admin.layouts.app')

@section('title', 'Tambah Pegawai')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Pegawai',
        'crumbs' => [
            ['label' => 'Pegawai', 'url' => route('admin.pegawai.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pegawai.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('admin.pegawai._form', ['jabatans' => $jabatans])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.pegawai.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
