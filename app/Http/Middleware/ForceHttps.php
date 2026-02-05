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
            // Obtener el host sin puerto
            $host = $request->getHost();
            
            // Remover el puerto si está presente (ej: mafit.regiontamaulipas.com.mx:443)
            if (str_contains($host, ':')) {
                $host = explode(':', $host)[0];
            }
            
            // Configurar la URL base sin puerto (443 es el puerto por defecto de HTTPS)
            $rootUrl = 'https://' . $host;
            
            // Forzar esquema HTTPS y URL base sin puerto
            URL::forceScheme('https');
            URL::forceRootUrl($rootUrl);
            
            // Forzar que el puerto por defecto sea null (no se incluirá en URLs)
            // Esto previene que Laravel incluya :443 en las URLs generadas
            $request->server->set('SERVER_PORT', 443);
            $request->server->set('HTTPS', 'on');
            
            // Actualizar APP_URL dinámicamente
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
