<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MAFIT') }} - @yield('title', 'Sistema de Control de Activos Fijos')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    <!-- Scripts -->
    @php
        // Detectar HTTPS y forzar esquema antes de que Vite genere las URLs
        $isSecure = request()->isSecure() || 
                    str_starts_with(request()->url(), 'https://') ||
                    request()->header('X-Forwarded-Proto') === 'https' ||
                    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        
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
    @endphp
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        // Toggle sidebar en móvil
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            if (overlay) {
                overlay.classList.toggle('hidden');
            }
        }
        
        // Cerrar sidebar al hacer clic en overlay
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('sidebar-overlay');
            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }

            // Función para colapsar todos los submenús excepto el especificado
            function colapsarOtrosSubmenus(exceptoId) {
                const submenus = [
                    { id: 'inventario-submenu', toggle: 'inventario-toggle' },
                    { id: 'inventario-psf-submenu', toggle: 'inventario-psf-toggle' },
                    { id: 'config-submenu', toggle: 'config-toggle' }
                ];
                
                submenus.forEach(submenu => {
                    if (submenu.id !== exceptoId) {
                        const submenuEl = document.getElementById(submenu.id);
                        const toggleEl = document.getElementById(submenu.toggle);
                        if (submenuEl && !submenuEl.classList.contains('hidden')) {
                            submenuEl.classList.add('hidden');
                            const icon = toggleEl ? toggleEl.querySelector('svg:last-child') : null;
                            if (icon) {
                                icon.classList.remove('rotate-90');
                            }
                        }
                    }
                });
            }

            // Toggle submenu de Inventario
            const inventarioToggle = document.getElementById('inventario-toggle');
            const inventarioSubmenu = document.getElementById('inventario-submenu');
            if (inventarioToggle && inventarioSubmenu) {
                inventarioToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const estaAbierto = !inventarioSubmenu.classList.contains('hidden');
                    
                    // Siempre colapsar otros submenús primero
                    colapsarOtrosSubmenus('inventario-submenu');
                    
                    // Luego hacer toggle del submenú actual
                    inventarioSubmenu.classList.toggle('hidden');
                    const icon = inventarioToggle.querySelector('svg:last-child');
                    if (icon) {
                        if (estaAbierto) {
                            icon.classList.remove('rotate-90');
                        } else {
                            icon.classList.add('rotate-90');
                        }
                    }
                });
            }

            // Toggle submenu de Inventario PFS
            const inventarioPSFToggle = document.getElementById('inventario-psf-toggle');
            const inventarioPSFSubmenu = document.getElementById('inventario-psf-submenu');
            if (inventarioPSFToggle && inventarioPSFSubmenu) {
                inventarioPSFToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const estaAbierto = !inventarioPSFSubmenu.classList.contains('hidden');
                    
                    // Siempre colapsar otros submenús primero
                    colapsarOtrosSubmenus('inventario-psf-submenu');
                    
                    // Luego hacer toggle del submenú actual
                    inventarioPSFSubmenu.classList.toggle('hidden');
                    const icon = inventarioPSFToggle.querySelector('svg:last-child');
                    if (icon) {
                        if (estaAbierto) {
                            icon.classList.remove('rotate-90');
                        } else {
                            icon.classList.add('rotate-90');
                        }
                    }
                });
            }

            // Toggle submenu de Configuración
            const configToggle = document.getElementById('config-toggle');
            const configSubmenu = document.getElementById('config-submenu');
            if (configToggle && configSubmenu) {
                configToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const estaAbierto = !configSubmenu.classList.contains('hidden');
                    
                    // Siempre colapsar otros submenús primero
                    colapsarOtrosSubmenus('config-submenu');
                    
                    // Luego hacer toggle del submenú actual
                    configSubmenu.classList.toggle('hidden');
                    const icon = configToggle.querySelector('svg:last-child');
                    if (icon) {
                        if (estaAbierto) {
                            icon.classList.remove('rotate-90');
                        } else {
                            icon.classList.add('rotate-90');
                        }
                    }
                });
            }
        });
    </script>
</head>
<body class="font-sans antialiased bg-gray-100">
    @auth
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-2xl">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-20 px-8 bg-slate-900/50 backdrop-blur-sm border-b border-white/5">
                    <div class="flex items-center gap-3">
                         <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <span class="font-bold text-lg">M</span>
                         </div>
                         <h1 class="text-xl font-bold tracking-tight">MAFIT</h1>
                    </div>
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-transparent">
                    @php
                        $user = auth()->user();
                        $userModules = $user ? $user->modules->pluck('name')->toArray() : [];
                        $isAdmin = $user && $user->isAdmin();
                    @endphp
                    
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4 px-4 mt-2">Menú Principal</div>

                    @if($isAdmin || in_array('dashboard', $userModules))
                    <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="font-medium">Inicio</span>
                    </a>
                    @endif

                    <!-- Inventario con submenú -->
                    @if($isAdmin || in_array('inventario.captura', $userModules) || in_array('inventario.consulta', $userModules) || in_array('inventario.realizados', $userModules))
                    <div class="space-y-1 pt-2">
                        <button id="inventario-toggle" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('inventario.*') ? 'bg-white/5 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('inventario.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span class="font-medium">Inventario</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('inventario.*') ? 'rotate-90 text-blue-400' : 'text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div id="inventario-submenu" class="{{ request()->routeIs('inventario.*') ? '' : 'hidden' }} space-y-1">
                            @if($isAdmin || in_array('inventario.captura', $userModules))
                            <a href="{{ route('inventario.captura') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('inventario.captura') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('inventario.captura') ? 'bg-blue-400' : 'bg-slate-600' }}"></span>
                                Captura Inventario
                            </a>
                            @endif
                            @if($isAdmin || in_array('inventario.consulta', $userModules))
                            <a href="{{ route('inventario.consulta') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('inventario.consulta') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('inventario.consulta') ? 'bg-blue-400' : 'bg-slate-600' }}"></span>
                                Consulta Inventario Real
                            </a>
                            @endif
                            @if($isAdmin || in_array('inventario.realizados', $userModules))
                            <a href="{{ route('inventario.realizados') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('inventario.realizados') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('inventario.realizados') ? 'bg-blue-400' : 'bg-slate-600' }}"></span>
                                Realizados
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Inventario PSF con submenú -->
                    @if($isAdmin || in_array('inventario-psf.captura', $userModules) || in_array('inventario-psf.consulta', $userModules) || in_array('inventario-psf.movimientos', $userModules))
                    <div class="space-y-1 pt-2">
                        <button id="inventario-psf-toggle" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('inventario-psf.*') ? 'bg-white/5 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('inventario-psf.*') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="font-medium">Inventario PFS</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('inventario-psf.*') ? 'rotate-90 text-indigo-400' : 'text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div id="inventario-psf-submenu" class="{{ request()->routeIs('inventario-psf.*') ? '' : 'hidden' }} space-y-1">
                            @if($isAdmin || in_array('inventario-psf.captura', $userModules))
                            <a href="{{ route('inventario-psf.captura') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('inventario-psf.captura') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('inventario-psf.captura') ? 'bg-indigo-400' : 'bg-slate-600' }}"></span>
                                Captura PFS
                            </a>
                            @endif
                            @if($isAdmin || in_array('inventario-psf.consulta', $userModules))
                            <a href="{{ route('inventario-psf.consulta') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('inventario-psf.consulta') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('inventario-psf.consulta') ? 'bg-indigo-400' : 'bg-slate-600' }}"></span>
                                Consulta PFS
                            </a>
                            @endif
                            @if($isAdmin || in_array('inventario-psf.movimientos', $userModules))
                            <a href="{{ route('inventario-psf.movimientos') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('inventario-psf.movimientos') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('inventario-psf.movimientos') ? 'bg-indigo-400' : 'bg-slate-600' }}"></span>
                                Movimientos PFS
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Movimientos de Equipos -->
                    @if($isAdmin || in_array('movimientos.index', $userModules) || in_array('movimientos.consulta', $userModules))
                    <div class="space-y-1 pt-2">
                         <div class="px-4 py-2">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gestión</span>
                        </div>
                        @if($isAdmin || in_array('movimientos.index', $userModules))
                        <a href="{{ route('movimientos.index') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('movimientos.index') ? 'bg-white/5 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('movimientos.index') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            <span class="font-medium">Movimiento Equipos</span>
                        </a>
                        @endif
                        @if($isAdmin || in_array('movimientos.consulta', $userModules))
                        <a href="{{ route('movimientos.consulta') }}" class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('movimientos.consulta') ? 'bg-white/5 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                             <svg class="w-5 h-5 mr-3 {{ request()->routeIs('movimientos.consulta') ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <span class="font-medium">Consulta Movs</span>
                        </a>
                        @endif
                    </div>
                    @endif

                    @can('admin')
                    <!-- Configuración con submenú -->
                    <div class="space-y-1 pt-2">
                        <div class="px-4 py-2">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sistema</span>
                        </div>
                        <button id="config-toggle" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.*') || request()->routeIs('maf.batches*') ? 'bg-white/5 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.*') || request()->routeIs('maf.batches*') ? 'text-rose-400' : 'text-slate-500 group-hover:text-rose-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="font-medium">Configuración</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('admin.*') || request()->routeIs('maf.batches*') ? 'rotate-90 text-rose-400' : 'text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div id="config-submenu" class="{{ request()->routeIs('admin.*') || request()->routeIs('maf.batches*') ? '' : 'hidden' }} space-y-1">
                            <a href="{{ route('admin.users.index') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.users*') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('admin.users*') ? 'bg-rose-400' : 'bg-slate-600' }}"></span>
                                Usuarios
                            </a>
                            <a href="{{ route('admin.tienda-assignment.index') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.tienda-assignment*') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('admin.tienda-assignment*') ? 'bg-rose-400' : 'bg-slate-600' }}"></span>
                                Asignación
                            </a>
                            <a href="{{ route('maf.batches.index') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('maf.batches*') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('maf.batches*') ? 'bg-rose-400' : 'bg-slate-600' }}"></span>
                                Importar MAF
                            </a>
                            <a href="{{ route('admin.categorias.index') }}" class="flex items-center pl-12 pr-4 py-2.5 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.categorias*') ? 'text-white bg-white/5 font-medium' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->routeIs('admin.categorias*') ? 'bg-rose-400' : 'bg-slate-600' }}"></span>
                                Categorías
                            </a>
                        </div>
                    </div>
                    @endcan
                </nav>

                <!-- User Info -->
                <div class="px-6 py-4 border-t border-white/5 bg-slate-900/50 backdrop-blur-md">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20 text-white font-bold ring-2 ring-slate-800">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 text-sm text-slate-300 rounded-lg hover:bg-white/10 hover:text-white transition-colors border border-white/5 hover:border-white/10 group">
                            <svg class="w-4 h-4 mr-2 group-hover:text-rose-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Overlay para móvil -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden hidden transition-opacity"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden bg-slate-50 relative z-0">
            <!-- Top Bar -->
            <header class="bg-white/80 backdrop-blur-xl border-b border-slate-200 h-16 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-40 shadow-sm">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-slate-800 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">@yield('page-title', 'Inicio')</h2>
                </div>
                
                <!-- Quick Actions / Notifications placeholder -->
                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center px-3 py-1 bg-slate-100 rounded-full border border-slate-200">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></div>
                        <span class="text-xs font-semibold text-slate-600">Sistema Activo</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-slate-50 p-4 lg:p-8">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @else
    <!-- Layout sin autenticación -->
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">MAFIT</h1>
                    </div>
                </div>
            </div>
        </nav>
        <main class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>
    @endauth

    <!-- Modal Global Notification/Confirmation -->
    <div id="modalNotificacionGlobal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <!-- Modal Panel -->
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="modalPanel">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div id="modalIconGlobal" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10 ring-8 ring-blue-50 transition-all duration-300">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-bold leading-6 text-slate-900" id="modalTituloGlobal">Título</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500" id="modalMensajeGlobal">Mensaje de notificación.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                        <button type="button" id="modalBtnConfirmar" class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-all hover:shadow-lg hover:shadow-blue-500/30 active:scale-95 ring-1 ring-inset ring-transparent">Aceptar</button>
                        <button type="button" id="modalBtnCancelar" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all active:scale-95 hidden">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Global State
    let onConfirmCallback = null;

    // Animation Helpers
    function animateIn() {
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');
        
        document.getElementById('modalNotificacionGlobal').classList.remove('hidden');
        
        // Force reflow
        void backdrop.offsetWidth;
        
        backdrop.classList.remove('opacity-0');
        panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
    }

    function animateOut() {
        const backdrop = document.getElementById('modalBackdrop');
        const panel = document.getElementById('modalPanel');
        
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
        panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
        
        setTimeout(() => {
            document.getElementById('modalNotificacionGlobal').classList.add('hidden');
        }, 200); 
    }

    // Main Function: Show Modal
    function mostrarModal(titulo, mensaje, tipo = 'info', confirmCallback = null) {
        // Elements
        const titleEl = document.getElementById('modalTituloGlobal');
        const msgEl = document.getElementById('modalMensajeGlobal');
        const iconEl = document.getElementById('modalIconGlobal');
        const btnConfirm = document.getElementById('modalBtnConfirmar');
        const btnCancel = document.getElementById('modalBtnCancelar');
        
        // Set Content
        titleEl.textContent = titulo;
        msgEl.textContent = mensaje;
        
        // Reset Icon Classes
        iconEl.className = 'mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10 ring-8 transition-all duration-300';
        btnConfirm.className = 'inline-flex w-full justify-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto transition-all hover:shadow-lg active:scale-95 ring-1 ring-inset ring-transparent';

        // Configure Type
        if (tipo === 'success') {
            iconEl.classList.add('bg-emerald-100', 'ring-emerald-50');
            iconEl.innerHTML = '<svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
            btnConfirm.classList.add('bg-emerald-600', 'hover:bg-emerald-500', 'hover:shadow-emerald-500/30');
            btnConfirm.textContent = 'Aceptar';
        } else if (tipo === 'error') {
            iconEl.classList.add('bg-rose-100', 'ring-rose-50');
            iconEl.innerHTML = '<svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>';
            btnConfirm.classList.add('bg-rose-600', 'hover:bg-rose-500', 'hover:shadow-rose-500/30');
            btnConfirm.textContent = 'Entendido';
        } else if (tipo === 'confirm') {
            iconEl.classList.add('bg-amber-100', 'ring-amber-50');
            iconEl.innerHTML = '<svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>';
            btnConfirm.classList.add('bg-blue-600', 'hover:bg-blue-500', 'hover:shadow-blue-500/30');
            btnConfirm.textContent = 'Confirmar';
        } else {
            // Info
            iconEl.classList.add('bg-blue-100', 'ring-blue-50');
            iconEl.innerHTML = '<svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>';
            btnConfirm.classList.add('bg-blue-600', 'hover:bg-blue-500', 'hover:shadow-blue-500/30');
            btnConfirm.textContent = 'Aceptar';
        }

        // Show/Hide Cancel Button
        if (confirmCallback) {
            btnCancel.classList.remove('hidden');
            onConfirmCallback = confirmCallback;
        } else {
            btnCancel.classList.add('hidden');
            onConfirmCallback = null;
        }

        animateIn();
    }

    function cerrarModal() {
        animateOut();
    }

    // Función para volver arriba (Moved up)
    function volverArriba() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Función global para mostrar el botón flotante
    function mostrarBotonVolverArriba() {
        const btnVolverArriba = document.getElementById('btnVolverArriba');
        if (btnVolverArriba) {
            btnVolverArriba.classList.remove('hidden', 'opacity-0', 'translate-y-4');
            btnVolverArriba.classList.add('opacity-100', 'translate-y-0');
        }
    }

    // Función global para ocultar el botón flotante
    function ocultarBotonVolverArriba() {
        const btnVolverArriba = document.getElementById('btnVolverArriba');
        if (btnVolverArriba) {
            btnVolverArriba.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => {
                if (window.pageYOffset <= 300) {
                    btnVolverArriba.classList.add('hidden');
                }
            }, 300);
        }
    }

    window.mostrarModal = mostrarModal;
    
    // Alias for explicit confirmation usage
    window.confirmModal = function(title, message, callback) {
        mostrarModal(title, message, 'confirm', callback);
    };

    // Events Listeners
    document.addEventListener('DOMContentLoaded', () => {
        // Modal Events
        const btnCancel = document.getElementById('modalBtnCancelar');
        const backdrop = document.getElementById('modalBackdrop');
        const btnConfirm = document.getElementById('modalBtnConfirmar');

        if(btnCancel) btnCancel.addEventListener('click', cerrarModal);
        if(backdrop) backdrop.addEventListener('click', cerrarModal);
        
        if(btnConfirm) {
            btnConfirm.addEventListener('click', () => {
                if (onConfirmCallback) {
                    onConfirmCallback();
                }
                cerrarModal();
            });
        }

        // Scroll Button Logic
        const btnVolverArriba = document.getElementById('btnVolverArriba');
        
        if (btnVolverArriba) {
            // Check initial position
            if (window.pageYOffset > 300) {
                mostrarBotonVolverArriba();
            }
            
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    mostrarBotonVolverArriba();
                } else {
                    ocultarBotonVolverArriba();
                }
            });
        }
    });
    </script>

    <!-- Botón Flotante Volver Arriba -->
    <button 
        id="btnVolverArriba" 
        onclick="volverArriba()"
        class="fixed bottom-8 right-8 z-[9999] hidden bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-all duration-300 opacity-0 transform translate-y-4"
        title="Volver arriba"
        aria-label="Volver arriba"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
</body>
</html>
