<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

use App\Models\Kelas; // Ensure you import the Kelas model


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('path.public', function() {
            // 1. If running as a web request, use the directory of the entry script (index.php)
            if (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
                $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME'])), '/');
                if (file_exists($scriptDir . '/index.php')) {
                    return $scriptDir;
                }
            }

            // 2. Fallback for CLI: if public_html exists inside the project base folder
            if (is_dir(base_path('public_html'))) {
                return base_path('public_html');
            }

            // 3. Fallback for CLI on shared hosting where public_html is a sibling of project base folder
            $siblingPublicHtml = dirname(base_path()) . '/public_html';
            if (is_dir($siblingPublicHtml)) {
                return $siblingPublicHtml;
            }

            // 4. Default fallback to Laravel public folder
            return base_path('public');
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         View::composer('*', function ($view) {
        $view->with('kelas', \App\Models\Kelas::all());
    });
     if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
    }
}
