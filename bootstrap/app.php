<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'mandant' => \App\Http\Middleware\EnsureUserHasMandant::class,
            'owner' => \App\Http\Middleware\EnsureUserIsOwner::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            $path = $request->path();

            if ($path === 'portal' || str_starts_with($path, 'portal/')) {
                return route('portal.login');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('rents:generate-monthly')->monthlyOn(1, '6:00');
    })
    ->create();
