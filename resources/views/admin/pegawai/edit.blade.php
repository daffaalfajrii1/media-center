@extends('admin.layouts.app')

@section('title', 'Edit Pegawai')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Pegawai',
        'crumbs' => [
            ['label' => 'Pegawai', 'url' => route('admin.pegawai.index')],
            ['label' => $pegawai->name],
        ],
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pegawai.update', $pegawai) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.pegawai._form', ['pegawai' => $pegawai, 'jabatans' => $jabatans])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.pegawai.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
