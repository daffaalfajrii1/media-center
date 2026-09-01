<?php

namespace App\Providers;

use App\Http\View\Composers\PublicMenuComposer;
use App\Http\View\Composers\WebsiteSettingComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('safeHtml', function (string $expression): string {
            return "<?php echo \\App\\Support\\HtmlSanitizer::clean($expression); ?>";
        });

        Paginator::defaultView('vendor.pagination.binduz');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');

        View::composer('public.*', PublicMenuComposer::class);
        View::composer(['public.*', 'admin.layouts.*', 'admin.layouts.partials.*', 'layouts.auth', 'auth.*', 'errors.*'], WebsiteSettingComposer::class);
        if (config('app.env') === 'production') {
            URL::forceScheme('https');

        }
    }
}
