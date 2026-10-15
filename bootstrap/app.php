<?php

use App\Exceptions\FleetException;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsClient;
use App\Http\Middleware\IsStaff;
use App\Http\Middleware\IsSuperAdmin;
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
            'admin'  => IsAdmin::class,
            'client' => IsClient::class,
            'staff'  => IsStaff::class,
            'super'  => IsSuperAdmin::class,
            'active' => CheckUserStatus::class,
        ]);

        // Yon moun ki deja konekte ki louvri /login oswa /register ale dirèk sou espas li.
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->homeRoute() ?? '/');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Se pa yon bug: se yon règ biznis. Pa ekri l nan laravel.log.
        $exceptions->dontReport(FleetException::class);

        // Règ biznis ki pa respekte (stock vid, move estati...) → retounen sou paj la ak mesaj la.
        $exceptions->render(function (FleetException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        });
    })->create();
