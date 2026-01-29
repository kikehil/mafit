<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Detectar HTTPS de múltiples formas
        $isSecure = false;
        
        // Verificar URL actual
        if (str_starts_with($request->url(), 'https://')) {
            $isSecure = true;
        }
        
        // Verificar headers de proxy
        if ($request->header('X-Forwarded-Proto') === 'https' || 
            $request->header('X-Forwarded-Ssl') === 'on') {
            $isSecure = true;
        }
        
        // Verificar variables de servidor
        $server = $request->server->all();
        if (isset($server['HTTP_X_FORWARDED_PROTO']) && $server['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $isSecure = true;
        }
        if (isset($server['HTTPS']) && ($server['HTTPS'] === 'on' || $server['HTTPS'] === '1')) {
            $isSecure = true;
        }
        if (isset($server['SERVER_PORT']) && $server['SERVER_PORT'] == 443) {
            $isSecure = true;
        }
        
        // Si es HTTPS, forzar esquema en todas las URLs
        if ($isSecure || $request->isSecure()) {
            URL::forceScheme('https');
            // Actualizar APP_URL dinámicamente
            $appUrl = config('app.url');
            if ($appUrl && str_starts_with($appUrl, 'http://')) {
                config(['app.url' => str_replace('http://', 'https://', $appUrl)]);
            }
        }
        
        $response = $next($request);
        
        // Si es HTTPS y la respuesta es HTML, reemplazar URLs HTTP por HTTPS
        if ($isSecure && $response->headers->get('Content-Type') && str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $content = $response->getContent();
            if ($content !== false) {
                // Obtener el dominio de la solicitud
                $host = $request->getHost();
                
                // Reemplazar URLs HTTP por HTTPS en el contenido HTML
                $content = str_replace(
                    [
                        'http://' . $host,
                        'href="http://',
                        'src="http://',
                        'action="http://',
                        "href='http://",
                        "src='http://",
                        "action='http://",
                    ],
                    [
                        'https://' . $host,
                        'href="https://',
                        'src="https://',
                        'action="https://',
                        "href='https://",
                        "src='https://",
                        "action='https://",
                    ],
                    $content
                );
                
                $response->setContent($content);
            }
        }
        
        return $response;
    }
}
