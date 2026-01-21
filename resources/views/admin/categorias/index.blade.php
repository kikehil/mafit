@extends('layouts.app')

@section('title', 'Catálogo de Categorías')
@section('page-title', 'Catálogo de Categorías')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Catálogo de Categorías</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.categorias.import') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Importar Catálogo
                </a>
                <a href="{{ route('admin.categorias.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Nueva Categoría
                </a>
            </div>
        </div>

        <!-- Búsqueda y Filtros -->
        <div class="mb-6">
            <form method="GET" action="{{ route('admin.categorias.index') }}" class="flex gap-2">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar por descripción o categoría..." 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <select 
                    name="activo" 
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                    <option value="">Todos</option>
                    <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Buscar
                </button>
                @if(request('q') || request('activo'))
                    <a href="{{ route('admin.categorias.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        @if($categorias->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actualizado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($categorias as $categoria)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $categoria->descripcion_raw }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $categoria->categoria }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($categoria->activo)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $categoria->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.categorias.edit', $categoria) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.categorias.destroy', $categoria) }}" class="inline" onsubmit="return confirm('¿Estás seguro de desactivar esta categoría?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Desactivar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $categorias->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">No se encontraron categorías.</p>
                @if(request('q') || request('activo'))
                    <p class="text-sm text-gray-400 mt-2">Intenta con otros términos de búsqueda.</p>
                @else
                    <p class="text-sm text-gray-400 mt-2">Usa el botón "Nueva Categoría" o "Importar Catálogo" para comenzar.</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection


