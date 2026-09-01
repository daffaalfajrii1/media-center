@extends('admin.layouts.app')

@section('title', 'Edit Agenda')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Agenda Kegiatan',
        'crumbs' => [
            ['label' => 'Agenda Kegiatan', 'url' => route('admin.agenda.index')],
            ['label' => 'Edit'],
        ],
    ])

    <div class="main-content">
        <div class="card">
            <div class="card-body">
                <form id="agenda-form" method="POST" action="{{ route('admin.agenda.update', $agenda) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.agenda._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('admin.agenda.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
