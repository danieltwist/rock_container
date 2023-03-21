<?php

namespace App\Providers;


use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Date\Date;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Date::setLocale(config('app.locale'));

        if(config('app.locale') == 'ru'){
            setlocale(LC_ALL, 'ru_RU.UTF-8');
            Carbon::setLocale('ru');
        }
        else {
            setlocale(LC_ALL, 'zh_ZH.UTF-8');
            Carbon::setLocale('zh');
        }

        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        \Blade::directive('nl2br', function ($string) {
            return "<?php echo nl2br($string); ?>";
        });

    }
}
