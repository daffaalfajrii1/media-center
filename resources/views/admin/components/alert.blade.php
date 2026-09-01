@if (session('success'))
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swal === 'undefined') return;

        Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary m-1',
            },
            buttonsStyling: false,
        }).fire({
            type: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            confirmButtonText: 'OK',
        });
    });
    </script>
    @endpush
@endif

@if (session('error'))
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swal === 'undefined') return;

        Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger m-1',
            },
            buttonsStyling: false,
        }).fire({
            type: 'error',
            title: 'Gagal',
            text: @json(session('error')),
            confirmButtonText: 'OK',
        });
    });
    </script>
    @endpush
@endif

@if ($errors->any())
    <div class="alert alert-danger m-4 mb-0" role="alert">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
