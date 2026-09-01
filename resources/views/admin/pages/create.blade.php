@extends('admin.layouts.app')

@section('title', 'Tambah Halaman')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Halaman',
        'crumbs' => [
            ['label' => 'Halaman Profil', 'url' => route('admin.halaman.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form id="page-form" method="POST" action="{{ route('admin.halaman.store') }}">
                    @csrf
                    @include('admin.pages._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.halaman.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@include('admin.pages._editor')
