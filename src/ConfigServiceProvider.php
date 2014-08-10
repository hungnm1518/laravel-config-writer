<?php

namespace October\Rain\Config;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['config'] = $this->app->share(function($app)
        {
            $loader = $app->getConfigLoader();
            $writer = new FileWriter($loader, $app['path'].'/config');
            return new Repository($loader, $writer, $app['env']);
        });
    }
}