@if ($websiteSettings->googleAnalyticsEnabled())
    @php $gaId = $websiteSettings->googleAnalyticsMeasurementId(); @endphp
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaId }}', {
            anonymize_ip: true,
            send_page_view: true,
        });
    </script>
@endif
