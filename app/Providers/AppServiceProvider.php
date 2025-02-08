<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\CSVUploadRepository;
use App\Interfaces\CSVUploadRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CSVUploadRepositoryInterface::class, CSVUploadRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
