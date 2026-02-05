@extends('layouts.app')

@section('title', 'Inventarios Realizados')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header with Glassmorphism -->
    <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-20 z-30 transition-all duration-300 hover:shadow-md">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Inventarios Realizados</h1>
            <p class="text-slate-500 text-sm mt-1">Historial de capturas completadas</p>
        </div>
        
        <!-- Search Bar -->
        <div class="w-full md:w-96">
            <form action="{{ route('inventario.realizados') }}" method="GET" class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="b" value="{{ request('b') }}" 
                    class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all shadow-sm group-hover:bg-white" 
                    placeholder="Buscar por tienda, CR o plaza...">
            </form>
        </div>
    </div>

    <!-- Results List -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h2 class="font-semibold text-slate-700">Resultados</h2>
            <span class="text-xs font-medium text-slate-500 bg-slate-200/50 px-2.5 py-1 rounded-full">{{ $inventarios->total() }} tiendas encontradas con inventario</span>
        </div>

        @if($inventarios->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($inventarios as $inv)
                <div class="p-6 hover:bg-slate-50 transition-colors duration-150 group relative">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <!-- Store Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-slate-900">{{ $inv['tienda'] }}</h3>
                                <span class="px-2.5 py-0.5 rounded-lg bg-blue-100 text-blue-700 text-xs font-bold font-mono tracking-wide border border-blue-200">
                                    {{ $inv['cr'] }}
                                </span>
                                @if($inv['plaza'])
                                <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium border border-slate-200">
                                    {{ $inv['plaza'] }}
                                </span>
                                @endif
                            </div>
                            
                            <div class="flex items-center text-sm text-slate-500 gap-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Último inventario: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($inv['fecha'])->format('d/m/Y H:i') }}</span></span>
                                <span class="mx-2 text-slate-300">|</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>por <span class="font-medium text-slate-700">{{ $inv['usuario_nombre'] ?? 'Desconocido' }}</span></span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3 self-start md:self-center">
                            <a href="{{ route('inventario.consulta', ['cr' => $inv['cr']]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 hover:text-blue-600 hover:border-blue-200 hover:shadow-md transition-all active:scale-95 group/btn">
                                <span>Ver Detalle</span>
                                <svg class="ml-2 -mr-1 w-4 h-4 text-slate-400 group-hover/btn:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <button type="button" onclick="window.mostrarModal('Próximamente', 'La función de exportar a Excel estará disponible pronto.', 'info')"
                               class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-xl text-slate-500 shadow-sm hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 hover:shadow-md transition-all active:scale-95"
                               title="Exportar Excel">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $inventarios->links() }}
            </div>
        @else
            <div class="p-12 text-center text-slate-500">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900">No se encontraron resultados</h3>
                <p class="mt-1 max-w-sm mx-auto">Intenta ajustar tu búsqueda o prueba con otros términos.</p>
            </div>
        @endif
    </div>
</div>

<!-- Scroll to Top Button -->
<button id="btnVolverArriba" 
        onclick="volverArriba()" 
        class="fixed bottom-8 right-8 z-50 p-3 rounded-full shadow-lg bg-blue-600 text-white hover:bg-blue-700 transition-all duration-300 transform translate-y-20 opacity-0 hidden hover:shadow-blue-500/30">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
    </svg>
</button>

<script>
    // Scroll to Top Logic
    const btnVolverArriba = document.getElementById('btnVolverArriba');

    if (btnVolverArriba) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                btnVolverArriba.classList.remove('hidden');
                // Small delay to allow display:block to apply before opacity transition
                setTimeout(() => {
                    btnVolverArriba.classList.remove('translate-y-20', 'opacity-0');
                }, 10);
            } else {
                btnVolverArriba.classList.add('translate-y-20', 'opacity-0');
                setTimeout(() => {
                    btnVolverArriba.classList.add('hidden');
                }, 300);
            }
        });
    }

    function volverArriba() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endsection
