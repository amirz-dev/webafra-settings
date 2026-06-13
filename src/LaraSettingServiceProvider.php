<?php

namespace Webafra\LaraSetting;

use Illuminate\Support\ServiceProvider;
use Webafra\LaraSetting\Setting as SettingService;

class LaraSettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/Database/Migrations/create_settings_table.php.stub' => database_path('migrations/' . date('Y_m_d_His') . '_create_settings_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/Models/' => app_path('Models'),
        ], 'models');

        $this->publishes([
            __DIR__ . '/Database/Migrations/create_settings_table.php.stub' => database_path('migrations/' . date('Y_m_d_His') . '_create_settings_table.php'),
            __DIR__ . '/Models/' => app_path('Models'),
        ], 'all');
    }

    public function register(): void
    {
        $this->app->singleton('webafra-settings', function ($app) {
            return new SettingService();
        });
    }
}
