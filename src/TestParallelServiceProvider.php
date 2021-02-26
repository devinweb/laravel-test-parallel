<?php

namespace Devinweb\TestParallel;

use Illuminate\Support\ServiceProvider;

class TestParallelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\ParallelCommand::class
        ]);
    }
}
