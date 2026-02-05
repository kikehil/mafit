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

        // Método 5: Forzar en Producción (Fix para VPS)
        // Si estamos en producción, asumir siempre HTTPS para evitar redirects mixtos (http:443)
        if (config('app.env') === 'production') {
            $isSecure = true;
        }
        
        // Si la solicitud es segura, forzar HTTPS en todas las URLs
        if ($isSecure) {
            // Obtener el host sin puerto
            $host = request()->getHost();
            
            // Remover el puerto si está presente (ej: mafit.regiontamaulipas.com.mx:443)
            if (str_contains($host, ':')) {
                $host = explode(':', $host)[0];
            }
            
            // Configurar la URL base sin puerto (443 es el puerto por defecto de HTTPS)
            $rootUrl = 'https://' . $host;
            
            // Forzar esquema HTTPS y URL base sin puerto
            URL::forceScheme('https');
            URL::forceRootUrl($rootUrl);
            
            // Forzar que el puerto por defecto sea 443 pero no se incluya en URLs
            request()->server->set('SERVER_PORT', 443);
            request()->server->set('HTTPS', 'on');
            
            // Actualizar APP_URL dinámicamente para que Vite lo use
            $appUrl = config('app.url');
            if ($appUrl) {
                // Remover puerto 443 si está presente
                $appUrl = preg_replace('/:443(\/|$)/', '$1', $appUrl);
                $appUrl = preg_replace('/:443$/', '', $appUrl);
                if (str_starts_with($appUrl, 'http://')) {
                    $appUrl = str_replace('http://', 'https://', $appUrl);
                }
                // Asegurar que no tenga puerto 443
                $appUrl = preg_replace('/:443(\/|$)/', '$1', $appUrl);
                $appUrl = preg_replace('/:443$/', '', $appUrl);
                config(['app.url' => $appUrl]);
            } else {
                config(['app.url' => $rootUrl]);
            }
        }
    }
}















