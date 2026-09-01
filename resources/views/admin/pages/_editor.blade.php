@push('styles')
@php $tinymceBase = 'https://cdn.jsdelivr.net/npm/tinymce@7.6.0'; @endphp
<link rel="stylesheet" href="{{ $tinymceBase }}/skins/ui/oxide/skin.min.css">
<style>
    .kecamatan-page-editor .tox-tinymce {
        border-radius: 8px;
        border-color: #e5e7eb;
    }
    .kecamatan-page-editor-status {
        font-size: 0.8125rem;
        color: #64748b;
        min-height: 1.25rem;
    }
    .kecamatan-page-editor-status.is-error { color: #dc2626; }
    .kecamatan-page-editor-status.is-saving { color: #d97706; }
</style>
@endpush

@push('scripts')
@php $tinymceBase = 'https://cdn.jsdelivr.net/npm/tinymce@7.6.0'; @endphp
<script src="{{ $tinymceBase }}/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const uploadUrl = @json(route('admin.halaman.editor-upload'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const statusEl = document.getElementById('page-editor-status');
    const form = document.getElementById('page-form');

    function setStatus(text, state) {
        if (!statusEl) return;
        statusEl.textContent = text;
        statusEl.classList.remove('is-error', 'is-saving');
        if (state) statusEl.classList.add(state);
    }

    async function uploadImage(blobInfo) {
        const formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename() || 'gambar.jpg');
        formData.append('_token', csrfToken);

        const response = await fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
            credentials: 'same-origin',
        });

        if (!response.ok) {
            let message = 'Gagal mengunggah gambar.';
            try {
                const data = await response.json();
                message = data.message || data.errors?.file?.[0] || message;
            } catch (e) { /* ignore */ }
            throw new Error(message);
        }

        const data = await response.json();
        const url = data.location || data.url;
        if (!url) throw new Error('Respons server tidak berisi URL gambar.');
        return url;
    }

    if (typeof tinymce === 'undefined') {
        setStatus('Editor gagal dimuat. Periksa koneksi internet lalu muat ulang.', 'is-error');
        return;
    }

    tinymce.init({
        selector: '#page-content',
        base_url: @json($tinymceBase),
        suffix: '.min',
        height: 420,
        menubar: 'edit view insert format table help',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic underline | '
            + 'alignleft aligncenter alignright | bullist numlist outdent indent | '
            + 'link image table | removeformat code fullscreen',
        branding: false,
        promotion: false,
        license_key: 'gpl',
        skin: 'oxide',
        content_style: 'body { font-family: "Plus Jakarta Sans", system-ui, sans-serif; font-size: 15px; line-height: 1.6; } img { max-width: 100%; height: auto; }',
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        automatic_uploads: true,
        paste_data_images: true,
        images_file_types: 'jpeg,jpg,png,gif,webp',
        images_upload_handler: function (blobInfo) {
            setStatus('Mengunggah gambar...', 'is-saving');
            return uploadImage(blobInfo)
                .then(function (url) {
                    setStatus('', null);
                    return url;
                })
                .catch(function (err) {
                    const msg = err?.message || 'Gagal mengunggah gambar.';
                    setStatus(msg, 'is-error');
                    return Promise.reject(msg);
                });
        },
        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype !== 'image') return;
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/gif,image/webp';
            input.onchange = async function () {
                const file = input.files?.[0];
                if (!file) return;
                try {
                    const url = await uploadImage({
                        blob: function () { return file; },
                        filename: function () { return file.name; },
                    });
                    callback(url, { alt: file.name });
                } catch (err) {
                    alert(err?.message || 'Gagal mengunggah gambar.');
                }
            };
            input.click();
        },
    }).then(function () {
        if (form) {
            form.addEventListener('submit', function () {
                tinymce.triggerSave();
            });
        }
    }).catch(function (err) {
        console.error('TinyMCE error:', err);
        setStatus('Editor gagal dimuat. Muat ulang halaman.', 'is-error');
    });
});
</script>
@endpush
