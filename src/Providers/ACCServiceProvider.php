<?php

namespace Wubbleyou\AccessControlChecker\Providers;

use Wubbleyou\AccessControlChecker\Console\Commands\GenerateTest;
use Wubbleyou\AccessControlChecker\Console\Commands\GenerateRule;
use Wubbleyou\AccessControlChecker\Console\Commands\GenerateRouteTrait;
use Wubbleyou\AccessControlChecker\Console\Commands\MissingRoutes;
use Illuminate\Support\ServiceProvider;

class ACCServiceProvider extends ServiceProvider {
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateRouteTrait::class,
                GenerateTest::class,
                GenerateRule::class,
                MissingRoutes::class,
            ]);
        }
    }
}