<?php

namespace HungNM\LCW\Config;

use Laravel\Lumen\Application;
use HungNM\LCW\Config\ServiceProvider;

class LumenServiceProvider extends ServiceProvider
{
    /** @var  Application */
    protected $app;

    protected function getConfigPath(): string
    {
        return $this->app->getConfigurationPath();
    }
}
