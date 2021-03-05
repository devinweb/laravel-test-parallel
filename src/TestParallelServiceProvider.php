<?php

namespace Devinweb\TestParallel;

use Devinweb\TestParallel\Console\ParallelCommand;
use Devinweb\TestParallel\Traits\TestDatabases;
use Illuminate\Support\ServiceProvider;

class TestParallelServiceProvider extends ServiceProvider
{
    use TestDatabases;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->app->singleton('parallelTesting', function () {
                return new ParallelTesting($this->app);
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        if ($this->app->runningInConsole()) {
            $this->bootTestDatabase();
        }

        $this->commands([
            ParallelCommand::class,
        ]);
    }
}
