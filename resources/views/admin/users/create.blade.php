@extends('admin.layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Pengguna',
        'crumbs' => [
            ['label' => 'Pengguna', 'url' => route('admin.pengguna.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pengguna.store') }}">
                    @csrf
                    @include('admin.users._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
