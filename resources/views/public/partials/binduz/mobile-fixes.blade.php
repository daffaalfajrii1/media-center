<script>
(function ($) {
    var $body = $('body');

    $('.binduz-er-news-canvas_open').on('click', function () {
        $body.addClass('binduz-er-menu-open');
    });

    $('.binduz-er-news-canvas_close, .binduz-er-news-off_canvars_overlay').on('click', function () {
        $body.removeClass('binduz-er-menu-open');
    });
})(jQuery);
</script>
