<div class="card border-0 shadow-sm mb-4" id="google-analytics">
    <div class="card-header bg-transparent border-bottom">
        <h5 class="mb-0"><i class="feather-bar-chart-2 me-2 text-primary"></i>Google Analytics 4</h5>
    </div>
    <div class="card-body">
        <div class="form-check form-switch mb-3">
            <input type="checkbox"
                   name="google_analytics_enabled"
                   value="1"
                   form="pengaturan-form"
                   class="form-check-input"
                   id="google-analytics-enabled"
                   @checked(old('google_analytics_enabled', $settings->google_analytics_enabled))>
            <label class="form-check-label" for="google-analytics-enabled">Aktifkan pelacakan Google Analytics di website publik</label>
        </div>
        <div class="mb-0">
            <label class="fw-semibold" for="google-analytics-id">Measurement ID (GA4)</label>
            <input type="text"
                   name="google_analytics_measurement_id"
                   id="google-analytics-id"
                   form="pengaturan-form"
                   class="form-control"
                   value="{{ old('google_analytics_measurement_id', $settings->google_analytics_measurement_id) }}"
                   placeholder="G-XXXXXXXXXX">
            <div class="form-text">
                Buat properti di
                <a href="https://analytics.google.com/" target="_blank" rel="noopener">Google Analytics</a>
                → Admin → Data Streams → salin <strong>Measurement ID</strong> (format <code>G-XXXXXXXXXX</code>).
            </div>
            @error('google_analytics_measurement_id')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
