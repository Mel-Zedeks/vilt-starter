<?php

namespace MelZedeks\ViltStarter;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ViltStarterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }
        $this->commands([
            Console\InstallCommand::class,
        ]);
    }

    public function provides()
    {
        return [Console\InstallCommand::class];
    }
}
