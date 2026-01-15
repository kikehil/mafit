<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MAFIT') }} - @yield('title', 'Sistema de Control de Activos Fijos')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
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
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-6 bg-gray-800 border-b border-gray-700">
                    <h1 class="text-xl font-bold">MAFIT</h1>
                    <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    @php
                        $user = auth()->user();
                        $userModules = $user ? $user->modules->pluck('name')->toArray() : [];
                        $isAdmin = $user && $user->isAdmin();
                    @endphp
                    
                    @if($isAdmin || in_array('dashboard', $userModules))
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Inicio
                    </a>
                    @endif
                    <!-- Inventario con submenú -->
                    @if($isAdmin || in_array('inventario.captura', $userModules) || in_array('inventario.consulta', $userModules) || in_array('inventario.realizados', $userModules))
                    <div class="space-y-1">
                        <button id="inventario-toggle" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('inventario.*') ? 'bg-gray-800 text-white' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span>Inventario</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform {{ request()->routeIs('inventario.*') ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div id="inventario-submenu" class="{{ request()->routeIs('inventario.*') ? '' : 'hidden' }} pl-4 space-y-1">
                            @if($isAdmin || in_array('inventario.captura', $userModules))
                            <a href="{{ route('inventario.captura') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('inventario.captura') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Captura Inventario
                            </a>
                            @endif
                            @if($isAdmin || in_array('inventario.consulta', $userModules))
                            <a href="{{ route('inventario.consulta') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('inventario.consulta') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Consulta Inventario
                            </a>
                            @endif
                            @if($isAdmin || in_array('inventario.realizados', $userModules))
                            <a href="{{ route('inventario.realizados') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('inventario.realizados') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Realizados
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                    <!-- Inventario PSF con submenú -->
                    @if($isAdmin || in_array('inventario-psf.captura', $userModules) || in_array('inventario-psf.consulta', $userModules) || in_array('inventario-psf.movimientos', $userModules))
                    <div class="space-y-1">
                        <button id="inventario-psf-toggle" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('inventario-psf.*') ? 'bg-gray-800 text-white' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Inventario PFS</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform {{ request()->routeIs('inventario-psf.*') ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div id="inventario-psf-submenu" class="{{ request()->routeIs('inventario-psf.*') ? '' : 'hidden' }} pl-4 space-y-1">
                            @if($isAdmin || in_array('inventario-psf.captura', $userModules))
                            <a href="{{ route('inventario-psf.captura') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('inventario-psf.captura') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Captura Inventario PFS
                            </a>
                            @endif
                            @if($isAdmin || in_array('inventario-psf.consulta', $userModules))
                            <a href="{{ route('inventario-psf.consulta') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('inventario-psf.consulta') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Consulta Inventario PFS
                            </a>
                            @endif
                            @if($isAdmin || in_array('inventario-psf.movimientos', $userModules))
                            <a href="{{ route('inventario-psf.movimientos') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('inventario-psf.movimientos') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Movimientos Inv PFS
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                    <!-- Movimientos de Equipos -->
                    @if($isAdmin || in_array('movimientos.index', $userModules) || in_array('movimientos.consulta', $userModules))
                    <div class="space-y-1">
                        @if($isAdmin || in_array('movimientos.index', $userModules))
                        <a href="{{ route('movimientos.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('movimientos.index') ? 'bg-gray-800 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            Movimiento de Equipos
                        </a>
                        @endif
                        @if($isAdmin || in_array('movimientos.consulta', $userModules))
                        <a href="{{ route('movimientos.consulta') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('movimientos.consulta') ? 'bg-gray-800 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Consulta Movimientos
                        </a>
                        @endif
                    </div>
                    @endif
                    @can('admin')
                    <!-- Configuración con submenú -->
                    <div class="space-y-1">
                        <button id="config-toggle" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.users*') || request()->routeIs('admin.tienda-assignment*') || request()->routeIs('maf.batches*') ? 'bg-gray-800 text-white' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Configuración</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform {{ request()->routeIs('admin.users*') || request()->routeIs('admin.tienda-assignment*') || request()->routeIs('maf.batches*') ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div id="config-submenu" class="{{ request()->routeIs('admin.users*') || request()->routeIs('admin.tienda-assignment*') || request()->routeIs('maf.batches*') ? '' : 'hidden' }} pl-4 space-y-1">
                            <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.users*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Usuarios
                            </a>
                            <a href="{{ route('admin.tienda-assignment.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.tienda-assignment*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Asignación de Tiendas
                            </a>
                            <a href="{{ route('maf.batches.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('maf.batches*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Importar MAF
                            </a>
                            <a href="{{ route('admin.categorias.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors {{ request()->routeIs('admin.categorias*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Catálogo de Categorías
                            </a>
                        </div>
                    </div>
                    @endcan
                </nav>

                <!-- User Info -->
                <div class="px-4 py-4 border-t border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                                <span class="text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Overlay para móvil -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 lg:px-8">
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h2 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Inicio')</h2>
                <div class="w-6"></div> <!-- Spacer para centrar -->
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-4 lg:p-8">
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

    <!-- Modal de Notificación Global -->
    <div id="modalNotificacionGlobal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div id="modalOverlayGlobal" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

            <!-- Modal centrado -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div id="modalIconGlobal" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Icono se llenará dinámicamente -->
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 id="modalTituloGlobal" class="text-lg leading-6 font-medium text-gray-900"></h3>
                            <div class="mt-2">
                                <p id="modalMensajeGlobal" class="text-sm text-gray-500"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        type="button" 
                        id="modalBtnCerrarGlobal"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Función global para mostrar modal de notificación
    function mostrarModal(titulo, mensaje, tipo = 'info') {
        const modal = document.getElementById('modalNotificacionGlobal');
        const modalTitulo = document.getElementById('modalTituloGlobal');
        const modalMensaje = document.getElementById('modalMensajeGlobal');
        const modalIcon = document.getElementById('modalIconGlobal');
        
        if (!modal || !modalTitulo || !modalMensaje || !modalIcon) {
            // Si no existe el modal global, usar alert como fallback
            alert(titulo + ': ' + mensaje);
            return;
        }
        
        modalTitulo.textContent = titulo;
        modalMensaje.textContent = mensaje;
        
        // Limpiar clases previas
        modalIcon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10';
        
        // Configurar icono según el tipo
        if (tipo === 'success') {
            modalIcon.classList.add('bg-green-100');
            modalIcon.innerHTML = `
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            `;
        } else if (tipo === 'error') {
            modalIcon.classList.add('bg-red-100');
            modalIcon.innerHTML = `
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            `;
        } else {
            modalIcon.classList.add('bg-blue-100');
            modalIcon.innerHTML = `
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
        }
        
        modal.classList.remove('hidden');
    }

    function cerrarModal() {
        const modal = document.getElementById('modalNotificacionGlobal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Inicializar eventos del modal global
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('modalOverlayGlobal');
        const btnCerrar = document.getElementById('modalBtnCerrarGlobal');
        
        if (overlay) {
            overlay.addEventListener('click', cerrarModal);
        }
        if (btnCerrar) {
            btnCerrar.addEventListener('click', cerrarModal);
        }
    });

    // Función para volver arriba
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

    // Mostrar/ocultar botón flotante según el scroll
    document.addEventListener('DOMContentLoaded', function() {
        const btnVolverArriba = document.getElementById('btnVolverArriba');
        
        if (btnVolverArriba) {
            // Verificar posición inicial
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
