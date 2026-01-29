<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'module' => \App\Http\Middleware\CheckModulePermission::class,
        ]);
        
        // Configurar TrustProxies para detectar HTTPS correctamente
        $middleware->trustProxies(at: '*');
        $middleware->trustHosts(at: ['*']);
        
        // Agregar middleware para forzar HTTPS cuando sea necesario (al inicio del stack)
        $middleware->web(prepend: [
            \App\Http\Middleware\ForceHttps::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();











