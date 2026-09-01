@php $duralux = asset('duralux/assets'); @endphp
<script src="{{ $duralux }}/vendors/js/vendors.min.js"></script>
<script src="{{ $duralux }}/js/common-init.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const profileWrap = document.querySelector('.kecamatan-profile-wrap');
        const profileToggle = document.querySelector('.kecamatan-profile-toggle');

        if (profileWrap && profileToggle) {
            profileToggle.addEventListener('click', function (e) {
                e.preventDefault();
                profileWrap.classList.toggle('is-open');
            });

            document.addEventListener('click', function (e) {
                if (!profileWrap.contains(e.target)) {
                    profileWrap.classList.remove('is-open');
                }
            });
        }
    });
</script>
