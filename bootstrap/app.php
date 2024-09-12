<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAPIToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(using: function () {
        $namespace = 'App\\Http\\Controllers';

        $version = config('base.conf.version');
        $service = config('base.conf.service');

        Route::match(['get', 'post'], 'testing', "$namespace\\Controller@testing");

        Route::prefix(config('base.conf.prefix.web') . "/$version/$service")
            ->middleware(['web'])
            ->namespace("$namespace\\" . config('base.conf.namespace.web'))
            ->group(base_path('routes/web.php'));

        Route::prefix(config('base.conf.prefix.mobile') . "/$version/$service")
            ->middleware(['web'])
            ->namespace("$namespace\\" . config('base.conf.namespace.mobile'))
            ->group(base_path('routes/mobile.php'));

    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: ['api/*']);
        $middleware->alias(
            [
                'auth.api' => CheckAPIToken::class
            ]    
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withSchedule(function () {
        //
    })->create();
