<?php

namespace Webafra\LaraSetting;

use Illuminate\Support\ServiceProvider;
use Webafra\LaraSetting\Models\Setting as SettingModel;

class LaraSettingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/Database/Migrations/create_settings_table.php.stub' => database_path('migrations/' . date('Y_m_d_His') . '_create_settings_table.php')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/Models/' => app_path('Models'),
        ], 'models');

        $this->publishes([
            __DIR__ . '/Database/Migrations/create_settings_table.php.stub' => database_path('migrations/' . date('Y_m_d_His') . '_create_settings_table.php'),
            __DIR__ . '/Models/' => app_path('Models'),
        ], 'all');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton('webafra-settings', function ($app) {
            return new SettingModel();
        });
    }
}
