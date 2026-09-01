@extends('admin.layouts.app')

@section('title', 'Tambah Pengumuman')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Pengumuman',
        'crumbs' => [
            ['label' => 'Pengumuman', 'url' => route('admin.pengumuman.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form id="pengumuman-form" method="POST" action="{{ route('admin.pengumuman.store') }}">
                    @csrf
                    @include('admin.pengumuman._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan & Tambah PDF</button>
                        <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
