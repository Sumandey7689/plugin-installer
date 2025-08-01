<?php

namespace PluginInstaller;

use Illuminate\Support\ServiceProvider;

class PluginInstallerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'plugininstaller');

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/plugininstaller'),
        ], 'plugininstaller-views');

        $this->publishes([
            __DIR__ . '/../plugins' => base_path('plugins'),
        ], 'plugininstaller-plugins');

        $this->registerPluginProviders();
    }

    public function register()
    {
        if (file_exists(__DIR__ . '/helpers.php')) {
            require_once __DIR__ . '/helpers.php';
        }
    }

    protected function registerPluginProviders()
    {
        $pluginsDir = base_path('plugins');
        if (!is_dir($pluginsDir))
            return;

        foreach (glob("$pluginsDir/*", GLOB_ONLYDIR) as $pluginPath) {
            $pluginJson = $pluginPath . '/plugin.json';
            if (file_exists($pluginJson)) {
                $config = json_decode(file_get_contents($pluginJson), true);
                $provider = $config['service_provider'] ?? null;

                if ($provider && class_exists($provider)) {
                    app()->register($provider);
                }
            }
        }
    }
}
