<?php

namespace App\Providers;

use App\Actions\ExtractCustomerNamesFromCsvFileAction;
use App\Actions\ExtractCustomerNamesFromCsvFileInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     */
    public array $bindings = [
        ExtractCustomerNamesFromCsvFileInterface::class => ExtractCustomerNamesFromCsvFileAction::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
