<?php
/**
 * Contoh shortcode WordPress: [mc_berita_terbaru limit="4"]
 *
 * Cara pakai:
 * 1. Buat token di Media Center: php artisan api:token admin@email.go.id --name=wordpress
 * 2. Simpan token di wp-config.php:
 *    define('MC_API_BASE', 'https://mediacenter.rejanglebongkab.go.id/api/v1');
 *    define('MC_API_TOKEN', 'token-anda-di-sini');
 * 3. Salin fungsi di bawah ke functions.php tema child, atau plugin kustom.
 * 4. Pasang shortcode [mc_berita_terbaru] di halaman/widget.
 */

add_shortcode('mc_berita_terbaru', function ($atts) {
    $atts = shortcode_atts([
        'limit' => 4,
    ], $atts, 'mc_berita_terbaru');

    $base = defined('MC_API_BASE') ? MC_API_BASE : 'https://mediacenter.rejanglebongkab.go.id/api/v1';
    $token = defined('MC_API_TOKEN') ? MC_API_TOKEN : '';

    if ($token === '') {
        return '<p>Token API Media Center belum diset.</p>';
    }

    $url = add_query_arg('limit', (int) $atts['limit'], rtrim($base, '/').'/berita/terbaru');

    $response = wp_remote_get($url, [
        'timeout' => 15,
        'headers' => [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ],
    ]);

    if (is_wp_error($response)) {
        return '<p>Gagal memuat berita dari Media Center.</p>';
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);

    if ($code !== 200 || empty($body['data']) || ! is_array($body['data'])) {
        return '<p>Berita belum tersedia.</p>';
    }

    ob_start();
    ?>
    <div class="mc-berita-terbaru">
        <h2 class="mc-berita-terbaru__title">Berita Terbaru</h2>
        <div class="mc-berita-terbaru__grid">
            <?php foreach ($body['data'] as $item) : ?>
                <article class="mc-berita-card">
                    <?php if (! empty($item['featured_image'])) : ?>
                        <a href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="noopener">
                            <img src="<?php echo esc_url($item['featured_image']); ?>"
                                 alt="<?php echo esc_attr($item['title']); ?>"
                                 loading="lazy">
                        </a>
                    <?php endif; ?>
                    <h3>
                        <a href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="noopener">
                            <?php echo esc_html($item['title']); ?>
                        </a>
                    </h3>
                    <?php if (! empty($item['published_at_formatted'])) : ?>
                        <time datetime="<?php echo esc_attr($item['published_at'] ?? ''); ?>">
                            <?php echo esc_html($item['published_at_formatted']); ?>
                        </time>
                    <?php endif; ?>
                    <a class="mc-berita-card__more" href="<?php echo esc_url($item['read_more_url']); ?>" target="_blank" rel="noopener">
                        Read More &gt;
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php

    return ob_get_clean();
});
