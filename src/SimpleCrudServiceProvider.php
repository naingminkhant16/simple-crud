<?php

namespace NaingMinKhant\SimpleCrud;

use Illuminate\Support\ServiceProvider;
class SimpleCrudServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCrudCommand::class,
            ]);
        }
    }
}
