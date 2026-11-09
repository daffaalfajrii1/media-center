<?php
/**
 * Shortcode Berita Terbaru — Media Center → WordPress (Avada)
 *
 * =====================================================================
 * SETUP wp-config.php (sebelum "That's all, stop editing!")
 * =====================================================================
 *
 * // Internal (WAJIB dari server WordPress 10.90.120.19)
 * define('MC_API_BASE', 'http://10.90.120.9/api/v1');
 * define('MC_API_HOST', 'mediacenter.rejanglebongkab.go.id');
 * define('MC_API_TOKEN', 'token-dari-admin-media-center');
 * define('MC_PUBLIC_URL', 'https://mediacenter.rejanglebongkab.go.id'); // foto untuk pengunjung
 *
 * Jangan pakai https://mediacenter... sebagai MC_API_BASE dari server WP
 * (IP publik sering Connection refused / hairpin NAT).
 * Foto di browser tetap harus pakai MC_PUBLIC_URL (HTTPS publik).
 *
 * =====================================================================
 * PASANG
 * =====================================================================
 * 1. Salin file ini ke child theme Avada, lalu di functions.php:
 *    require_once get_stylesheet_directory() . '/wordpress-berita-terbaru.php';
 *    ATAU tempel seluruh isi di bawah ke functions.php child theme.
 * 2. Di halaman Avada: [mc_berita_terbaru limit="4" title="Berita Terbaru"]
 * 3. Hapus cache Avada / plugin cache setelah ubah token.
 */

if (! defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    if (is_admin()) {
        return;
    }

    $css = '
.mc-berita-wrap{max-width:1200px;margin:0 auto;padding:24px 16px 40px;font-family:inherit}
.mc-berita-wrap__title{text-align:center;color:#1e3a8a;font-size:clamp(1.6rem,3vw,2.2rem);font-weight:800;margin:0 0 28px;letter-spacing:.02em}
.mc-berita-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px}
@media (max-width:1024px){.mc-berita-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:640px){.mc-berita-grid{grid-template-columns:1fr}}
.mc-berita-card{background:#fff;border:1px solid #c7d2fe;border-radius:4px;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .2s ease,transform .2s ease}
.mc-berita-card:hover{box-shadow:0 10px 28px rgba(30,58,138,.12);transform:translateY(-2px)}
.mc-berita-card__media{display:block;aspect-ratio:16/10;overflow:hidden;background:#e2e8f0}
.mc-berita-card__media img{width:100%;height:100%;object-fit:cover;display:block}
.mc-berita-card__body{padding:14px 14px 16px;display:flex;flex-direction:column;gap:8px;flex:1}
.mc-berita-card__title{margin:0;font-size:15px;line-height:1.45;font-weight:700}
.mc-berita-card__title a{color:#1e3a8a;text-decoration:none}
.mc-berita-card__title a:hover{color:#2563eb;text-decoration:underline}
.mc-berita-card__date{font-size:12px;color:#64748b}
.mc-berita-card__more{margin-top:auto;font-size:13px;font-weight:600;color:#334155;text-decoration:none}
.mc-berita-card__more:hover{color:#1d4ed8}
.mc-berita-empty,.mc-berita-error{text-align:center;color:#64748b;padding:24px}
';

    wp_register_style('mc-berita-terbaru', false, [], '1.1.0');
    wp_enqueue_style('mc-berita-terbaru');
    wp_add_inline_style('mc-berita-terbaru', $css);
});

/**
 * Konfigurasi API Media Center (pola sama seperti Layanko).
 *
 * @return array{base:string,host:string,token:string,public:string}
 */
function mc_berita_api_config()
{
    return [
        'base' => defined('MC_API_BASE')
            ? rtrim((string) MC_API_BASE, '/')
            : 'http://10.90.120.9/api/v1',
        'host' => defined('MC_API_HOST')
            ? (string) MC_API_HOST
            : 'mediacenter.rejanglebongkab.go.id',
        'token' => defined('MC_API_TOKEN') ? (string) MC_API_TOKEN : '',
        // Domain publik untuk foto/link (browser pengunjung, bukan IP internal)
        'public' => defined('MC_PUBLIC_URL')
            ? rtrim((string) MC_PUBLIC_URL, '/')
            : 'https://mediacenter.rejanglebongkab.go.id',
    ];
}

/**
 * Ubah URL internal (10.90.120.9 / http) jadi URL publik HTTPS
 * agar foto tampil di jaringan publik (bukan hanya VPN PDN).
 */
function mc_berita_public_media_url($url)
{
    if (! is_string($url) || $url === '') {
        return '';
    }

    $config = mc_berita_api_config();
    $public = $config['public'];

    $replacements = [
        'http://10.90.120.9',
        'https://10.90.120.9',
        'http://mediacenter.rejanglebongkab.go.id',
    ];

    foreach ($replacements as $from) {
        if (str_starts_with($url, $from)) {
            return $public . substr($url, strlen($from));
        }
    }

    return $url;
}

add_shortcode('mc_berita_terbaru', function ($atts) {
    $atts = shortcode_atts([
        'limit' => 4,
        'title' => 'Berita Terbaru',
        'cache' => 300,
        'debug' => '0',
    ], $atts, 'mc_berita_terbaru');

    $config = mc_berita_api_config();
    $debug = in_array(strtolower((string) $atts['debug']), ['1', 'true', 'yes'], true);

    if ($config['token'] === '') {
        return '<p class="mc-berita-error">Token API Media Center belum diset di wp-config.php (MC_API_TOKEN).</p>';
    }

    $limit = max(1, min(20, (int) $atts['limit']));
    $cache_key = 'mc_berita_terbaru_v2_' . md5($config['base'] . '|' . $config['host'] . '|' . $limit);
    $items = get_transient($cache_key);

    if ($items === false) {
        $url = add_query_arg('limit', $limit, $config['base'] . '/berita/terbaru');

        $response = wp_remote_get($url, [
            'timeout' => 20,
            'redirection' => 0,
            'sslverify' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $config['token'],
                // Wajib: satu IP banyak app (Media Center + Layanko)
                'Host' => $config['host'],
            ],
        ]);

        if (is_wp_error($response)) {
            $msg = $response->get_error_message();

            return '<p class="mc-berita-error">Gagal memuat berita dari Media Center.'
                . ($debug ? ' (' . esc_html($msg) . ')' : '')
                . '</p>';
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $raw = wp_remote_retrieve_body($response);
        $body = json_decode($raw, true);

        if ($code !== 200 || empty($body['data']) || ! is_array($body['data'])) {
            $hint = $debug
                ? ' HTTP ' . $code . ' — ' . esc_html(wp_html_excerpt(wp_strip_all_tags($raw), 180))
                : '';

            return '<p class="mc-berita-empty">Berita belum tersedia.' . $hint . '</p>';
        }

        $items = $body['data'];
        set_transient($cache_key, $items, max(60, (int) $atts['cache']));
    }

    ob_start();
    ?>
    <section class="mc-berita-wrap">
        <?php if ($atts['title'] !== '') : ?>
            <h2 class="mc-berita-wrap__title"><?php echo esc_html($atts['title']); ?></h2>
        <?php endif; ?>

        <div class="mc-berita-grid">
            <?php foreach ($items as $item) : ?>
                <?php
                $link = mc_berita_public_media_url(
                    ! empty($item['url']) ? $item['url'] : (! empty($item['read_more_url']) ? $item['read_more_url'] : '#')
                );
                $image = mc_berita_public_media_url(
                    ! empty($item['featured_image']) ? $item['featured_image'] : (! empty($item['thumbnail']) ? $item['thumbnail'] : '')
                );
                ?>
                <article class="mc-berita-card">
                    <?php if ($image !== '') : ?>
                        <a class="mc-berita-card__media" href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener">
                            <img src="<?php echo esc_url($image); ?>"
                                 alt="<?php echo esc_attr($item['title'] ?? ''); ?>"
                                 loading="lazy"
                                 referrerpolicy="no-referrer">
                        </a>
                    <?php endif; ?>

                    <div class="mc-berita-card__body">
                        <h3 class="mc-berita-card__title">
                            <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener">
                                <?php echo esc_html($item['title'] ?? ''); ?>
                            </a>
                        </h3>

                        <?php if (! empty($item['published_at_formatted'])) : ?>
                            <time class="mc-berita-card__date" datetime="<?php echo esc_attr($item['published_at'] ?? ''); ?>">
                                <?php echo esc_html($item['published_at_formatted']); ?>
                            </time>
                        <?php endif; ?>

                        <a class="mc-berita-card__more" href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener">
                            Read More &gt;
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php

    return ob_get_clean();
});
