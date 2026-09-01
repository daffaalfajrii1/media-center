@extends('admin.layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Pengumuman',
        'crumbs' => [
            ['label' => 'Pengumuman', 'url' => route('admin.pengumuman.index')],
            ['label' => 'Edit'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form id="pengumuman-form" method="POST" action="{{ route('admin.pengumuman.update', $pengumuman) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.pengumuman._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-light">Kembali</a>
                    </div>
                </form>

                @include('admin.pengumuman._attachments')
            </div>
        </div>
    </div>
@endsection
