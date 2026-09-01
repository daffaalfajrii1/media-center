<div class="modal fade media-kontak-modal" id="kontakDetailModal" tabindex="-1" aria-labelledby="kontakDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="media-kontak-modal__head">
                    <span class="media-kontak-modal__icon" id="kontakModalIcon" aria-hidden="true">
                        <i class="fal fa-address-card"></i>
                    </span>
                    <h5 class="modal-title" id="kontakDetailModalLabel">Kontak</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="media-kontak-modal__value" id="kontakModalValue"></p>
                <div class="media-kontak-modal__map d-none" id="kontakModalMap"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <a href="#" class="binduz-er-main-btn media-kontak-modal__action d-none" id="kontakModalAction" target="_blank" rel="noopener"></a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var modal = document.getElementById('kontakDetailModal');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', function (event) {
        var trigger = event.relatedTarget;
        if (!trigger) return;

        var title = trigger.getAttribute('data-kontak-title') || 'Kontak';
        var icon = trigger.getAttribute('data-kontak-icon') || 'fa-address-card';
        var value = trigger.getAttribute('data-kontak-value') || '';
        var actionUrl = trigger.getAttribute('data-kontak-action') || '';
        var actionLabel = trigger.getAttribute('data-kontak-action-label') || 'Buka';
        var mapSrc = trigger.getAttribute('data-kontak-map') || '';

        document.getElementById('kontakDetailModalLabel').textContent = title;
        document.getElementById('kontakModalIcon').innerHTML = '<i class="fal ' + icon + '"></i>';
        document.getElementById('kontakModalValue').textContent = value;

        var mapWrap = document.getElementById('kontakModalMap');
        if (mapSrc) {
            mapWrap.classList.remove('d-none');
            mapWrap.innerHTML = '<iframe src="' + mapSrc + '" width="100%" height="220" style="border:0;border-radius:10px;" allowfullscreen loading="lazy" title="Peta lokasi"></iframe>';
        } else {
            mapWrap.classList.add('d-none');
            mapWrap.innerHTML = '';
        }

        var action = document.getElementById('kontakModalAction');
        if (actionUrl) {
            action.href = actionUrl;
            action.textContent = actionLabel;
            action.classList.remove('d-none');
        } else {
            action.href = '#';
            action.classList.add('d-none');
        }
    });
})();
</script>
@endpush
