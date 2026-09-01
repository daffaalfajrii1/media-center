@extends('admin.layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Pengguna',
        'crumbs' => [
            ['label' => 'Pengguna', 'url' => route('admin.pengguna.index')],
            ['label' => 'Edit'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pengguna.update', $user) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.users._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
