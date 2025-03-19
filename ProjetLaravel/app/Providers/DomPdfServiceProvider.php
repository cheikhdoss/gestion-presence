<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DomPdfServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('dompdf', function() {
            return new \Dompdf\Dompdf();
        });

        $this->app->bind('pdf', function() {
            return new \Dompdf\Dompdf();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 