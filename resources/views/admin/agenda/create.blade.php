@extends('admin.layouts.app')

@section('title', 'Tambah Agenda')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Agenda Kegiatan',
        'crumbs' => [
            ['label' => 'Agenda Kegiatan', 'url' => route('admin.agenda.index')],
            ['label' => 'Tambah'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form id="agenda-form" method="POST" action="{{ route('admin.agenda.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('admin.agenda._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.agenda.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
