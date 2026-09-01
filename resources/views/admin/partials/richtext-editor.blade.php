@php
    $uploadUrl = $uploadUrl ?? route('admin.halaman.editor-upload');
    $formId = $formId ?? 'content-form';
    $textareaId = $textareaId ?? 'content';
    $statusId = $statusId ?? 'editor-status';
    $content = $content ?? '';
    $maxImageKb = (int) ($maxImageKb ?? 10240);
    $allowMultipleImages = (bool) ($allowMultipleImages ?? true);
    $tinymceBase = 'https://cdn.jsdelivr.net/npm/tinymce@7.6.0';
@endphp

@push('styles')
<link rel="stylesheet" href="{{ $tinymceBase }}/skins/ui/oxide/skin.min.css">
<style>
    .kecamatan-richtext-editor .tox-tinymce {
        border-radius: 8px;
        border-color: #e5e7eb;
    }
    #{{ $statusId }} {
        font-size: 0.8125rem;
        color: #64748b;
        min-height: 1.25rem;
    }
    #{{ $statusId }}.is-error { color: #dc2626; }
    #{{ $statusId }}.is-saving { color: #d97706; }
</style>
@endpush

<div class="kecamatan-richtext-editor">
    <textarea id="{{ $textareaId }}" name="content" class="form-control" rows="12">{{ $content }}</textarea>
    <div id="{{ $statusId }}" class="mt-2" aria-live="polite"></div>
</div>

@push('scripts')
<script src="{{ $tinymceBase }}/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const uploadUrl = @json($uploadUrl);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const statusEl = document.getElementById(@json($statusId));
    const form = document.getElementById(@json($formId));
    const textareaId = @json($textareaId);
    const maxImageBytes = @json($maxImageKb) * 1024;
    const allowMultipleImages = @json($allowMultipleImages);

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
        selector: '#' + textareaId,
        base_url: @json($tinymceBase),
        suffix: '.min',
        height: 420,
        menubar: 'edit view insert format table help',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'quickbars'
        ],
        toolbar: 'undo redo | blocks | bold italic underline | '
            + 'alignleft aligncenter alignright | bullist numlist outdent indent | '
            + 'link image table | removeformat code fullscreen',
        quickbars_selection_toolbar: 'bold italic underline | alignleft aligncenter alignright | quicklink',
        quickbars_image_toolbar: 'alignleft aligncenter alignright',
        object_resizing: true,
        resize_img_proportional: true,
        image_advtab: true,
        image_caption: true,
        image_title: true,
        image_description: true,
        image_class_list: [
            { title: 'Lebar penuh', value: 'img-full' },
            { title: 'Sedang', value: 'img-medium' },
            { title: 'Kecil', value: 'img-small' },
        ],
        branding: false,
        promotion: false,
        license_key: 'gpl',
        skin: 'oxide',
        content_style: 'body { font-family: "Plus Jakarta Sans", system-ui, sans-serif; font-size: 15px; line-height: 1.6; } '
            + 'img { max-width: 100%; height: auto; } '
            + 'img.img-full { width: 100%; display: block; } '
            + 'img.img-medium { width: 60%; display: block; margin: 1rem auto; } '
            + 'img.img-small { width: 35%; display: block; margin: 1rem auto; } '
            + 'p img[style*="float: left"], p img[style*="float:left"] { margin: 0 1rem 1rem 0; } '
            + 'p img[style*="float: right"], p img[style*="float:right"] { margin: 0 0 1rem 1rem; }',
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
            if (allowMultipleImages) {
                input.multiple = true;
            }
            input.onchange = async function () {
                const files = Array.from(input.files || []);
                if (!files.length) return;

                const oversized = files.find(function (file) { return file.size > maxImageBytes; });
                if (oversized) {
                    alert('Setiap gambar maksimal 10 MB: ' + oversized.name);
                    return;
                }

                try {
                    setStatus('Mengunggah ' + files.length + ' gambar...', 'is-saving');
                    const urls = [];
                    for (const file of files) {
                        const url = await uploadImage({
                            blob: function () { return file; },
                            filename: function () { return file.name; },
                        });
                        urls.push({ url: url, name: file.name });
                    }

                    callback(urls[0].url, { alt: urls[0].name });

                    if (urls.length > 1 && typeof tinymce !== 'undefined') {
                        const editor = tinymce.get(textareaId);
                        if (editor) {
                            for (let i = 1; i < urls.length; i++) {
                                editor.insertContent(
                                    '<p><img src="' + urls[i].url + '" alt="' + (urls[i].name || '') + '" class="img-full"></p>'
                                );
                            }
                        }
                    }
                    setStatus('', null);
                } catch (err) {
                    const msg = err?.message || 'Gagal mengunggah gambar.';
                    setStatus(msg, 'is-error');
                    alert(msg);
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
