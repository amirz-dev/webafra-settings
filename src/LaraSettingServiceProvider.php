<?php

namespace Webafra\LaraSetting;

use Illuminate\Support\ServiceProvider;

class LaraSettingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/Database/Migrations/create_settings_table.php.stub' => database_path('migrations/' . date('Y_m_d_His') . '_create_settings_table.php')
        ], 'webafra-settings-migrations');

        $this->publishes([
            __DIR__ . '/Models/' => app_path('Models/Webafra'),
        ], 'webafra-settings-models');

        $this->publishes([
            __DIR__ . '/Database/Migrations/' => database_path('migrations'),
            __DIR__ . '/Models/' => app_path('Models/Webafra'),
        ], 'webafra-settings-all');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // 
    }
}
