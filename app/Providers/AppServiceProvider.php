<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Detectar HTTPS de múltiples formas
        $isSecure = false;
        
        // Método 1: Verificar URL actual
        $currentUrl = request()->url();
        if (str_starts_with($currentUrl, 'https://')) {
            $isSecure = true;
        }
        
        // Método 2: Verificar headers de proxy/load balancer
        $headers = request()->headers->all();
        if (isset($headers['x-forwarded-proto'][0]) && $headers['x-forwarded-proto'][0] === 'https') {
            $isSecure = true;
        }
        if (isset($headers['x-forwarded-ssl'][0]) && $headers['x-forwarded-ssl'][0] === 'on') {
            $isSecure = true;
        }
        
        // Método 3: Verificar variables de servidor
        $server = request()->server->all();
        if (isset($server['HTTP_X_FORWARDED_PROTO']) && $server['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $isSecure = true;
        }
        if (isset($server['HTTPS']) && ($server['HTTPS'] === 'on' || $server['HTTPS'] === '1')) {
            $isSecure = true;
        }
        if (isset($server['SERVER_PORT']) && $server['SERVER_PORT'] == 443) {
            $isSecure = true;
        }
        
        // Método 4: Verificar método isSecure de Laravel
        if (request()->isSecure()) {
            $isSecure = true;
        }
        
        // Si la solicitud es segura, forzar HTTPS en todas las URLs
        if ($isSecure) {
            URL::forceScheme('https');
            // Actualizar APP_URL dinámicamente para que Vite lo use
            $appUrl = config('app.url');
            if (str_starts_with($appUrl, 'http://')) {
                config(['app.url' => str_replace('http://', 'https://', $appUrl)]);
            }
        }
    }
}















