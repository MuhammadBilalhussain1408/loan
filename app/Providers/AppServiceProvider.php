<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLengthAwarePaginator();
        if (class_exists(\Laravel\Telescope\Telescope::class)) {
            \Laravel\Telescope\Telescope::ignoreMigrations();
        }
        Sanctum::ignoreMigrations();
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\Telescope::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureEmailSettings();
    }

    protected function configureEmailSettings()
    {

        // if (config('app.installed') && Schema::hasTable('settings')) {
        //     Config::set('mail.default', Setting::on('mysql')->where('setting_key', 'mail_mailer')->first()->setting_value ?: env('MAIL_MAILER'));
        //     Config::set('mail.mailers.smtp.host', Setting::on('mysql')->where('setting_key', 'mail_host')->first()->setting_value ?: env('MAIL_HOST'));
        //     Config::set('mail.mailers.smtp.port', Setting::on('mysql')->where('setting_key', 'mail_port')->first()->setting_value ?: env('MAIL_PORT'));
        //     Config::set('mail.mailers.smtp.username', Setting::on('mysql')->where('setting_key', 'mail_username')->first()->setting_value ?: env('MAIL_USERNAME'));
        //     Config::set('mail.mailers.smtp.password', Setting::on('mysql')->where('setting_key', 'mail_password')->first()->setting_value ?: env('MAIL_PASSWORD'));
        //     Config::set('mail.mailers.smtp.encryption', Setting::on('mysql')->where('setting_key', 'mail_encryption')->first()->setting_value ?: env('MAIL_ENCRYPTION'));
        //     Config::set('mail.from.address', Setting::on('mysql')->where('setting_key', 'mail_from_address')->first()->setting_value ?: env('MAIL_FROM_ADDRESS'));
        //     Config::set('mail.from.name', Setting::on('mysql')->where('setting_key', 'mail_from_name')->first()->setting_value ?: env('MAIL_FROM_NAME'));

        // }
    }

    protected function registerLengthAwarePaginator()
    {
        $this->app->bind(LengthAwarePaginator::class, function ($app, $values) {
            return new class(...array_values($values)) extends LengthAwarePaginator {
                public function only(...$attributes)
                {
                    return $this->transform(function ($item) use ($attributes) {
                        return $item->only($attributes);
                    });
                }

                public function transform($callback)
                {
                    $this->items->transform($callback);

                    return $this;
                }

                public function toArray()
                {
                    return [
                        'data' => $this->items->toArray(),
                        'links' => $this->links(),
                    ];
                }

                public function links($view = null, $data = [])
                {
                    $this->appends(Request::all());

                    $window = UrlWindow::make($this);

                    $elements = array_filter([
                        $window['first'],
                        is_array($window['slider']) ? '...' : null,
                        $window['slider'],
                        is_array($window['last']) ? '...' : null,
                        $window['last'],
                    ]);

                    return Collection::make($elements)->flatMap(function ($item) {
                        if (is_array($item)) {
                            return Collection::make($item)->map(function ($url, $page) {
                                return [
                                    'url' => $url,
                                    'label' => $page,
                                    'active' => $this->currentPage() === $page,
                                ];
                            });
                        } else {
                            return [
                                [
                                    'url' => null,
                                    'label' => '...',
                                    'active' => false,
                                ],
                            ];
                        }
                    })->prepend([
                        'url' => $this->previousPageUrl(),
                        'label' => 'Previous',
                        'active' => false,
                    ])->push([
                        'url' => $this->nextPageUrl(),
                        'label' => 'Next',
                        'active' => false,
                    ]);
                }
            };
        });
    }
}
