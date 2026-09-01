@extends('admin.layouts.app')

@section('title', 'Tambah Jabatan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Jabatan',
        'crumbs' => [
            ['label' => 'Pegawai', 'url' => route('admin.pegawai.index')],
            ['label' => 'Jabatan', 'url' => route('admin.jabatan.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.jabatan.store') }}">
                    @csrf
                    @include('admin.jabatan._form')
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.jabatan.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
