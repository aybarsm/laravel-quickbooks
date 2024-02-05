<?php

namespace Aybarsm\Laravel\QuickBooks;

use Aybarsm\Laravel\QuickBooks\Concretes\Exception as QuickBooksException;
use Aybarsm\Laravel\QuickBooks\Concretes\Manager;
use Aybarsm\Laravel\QuickBooks\Concretes\Profile;
use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksErrorInterface;
use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksManagerInterface;
use Aybarsm\Laravel\QuickBooks\Contracts\QuickBooksProfileInterface;
use Aybarsm\Laravel\QuickBooks\Services\QuickBooksHelper;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class QuickBooksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/quickbooks.php',
            'quickbooks'
        );

        $this->publishes([
            __DIR__.'/../config/quickbooks.php' => config_path('quickbooks.php'),
        ], 'quickbooks-config');

        $concretes = config('quickbooks.concretes', []);

        $concretes['error'] = class_exists($concretes['error']) ? $concretes['error'] : QuickBooksException::class;
        $this->app->bindIf(QuickBooksErrorInterface::class, $concretes['error']);

        $concretes['profile'] = class_exists($concretes['profile']) ? $concretes['profile'] : Profile::class;
        $this->app->bindIf(QuickBooksProfileInterface::class, $concretes['profile']);

        $concretes['manager'] = class_exists($concretes['manager']) ? $concretes['manager'] : Manager::class;
        $this->app->singletonIf(QuickBooksManagerInterface::class, fn () => new $concretes['manager']());

        $this->app->alias(QuickBooksManagerInterface::class, 'quickbooks');
    }

    public function boot(): void
    {
        if (QuickBooksHelper::isValueTrue(config('quickbooks.registerRoutes'))) {
            Route::prefix('api/quickbooks')
                ->middleware('api')
                ->name('api.quickbooks.')
                ->group(__DIR__.'/../routes/api.php');
        }
    }

    public function provides(): array
    {
        return [
            QuickBooksManagerInterface::class, 'quickbooks',
        ];
    }
}
