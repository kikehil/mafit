@extends('layouts.app')

@section('title', 'Asignar Tiendas')
@section('page-title', 'Asignar Tiendas')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Asignar Tiendas</h2>
            <p class="mt-2 text-sm text-gray-600">
                Usuario: <span class="font-medium">{{ $user->name }}</span> | 
                Plaza: <span class="font-medium">{{ $user->plazaRef ? $user->plazaRef->plaza_nom : $user->plaza }}</span>
            </p>
        </div>

        <form method="POST" action="{{ route('admin.tienda-assignment.update', $user) }}">
            @csrf
            @method('PUT')

            @if($tiendas->count() > 0)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Selecciona las tiendas de las que {{ $user->name }} es responsable:
                    </label>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @foreach($tiendas as $tienda)
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="tiendas[]" 
                                    value="{{ $tienda->id }}"
                                    {{ in_array($tienda->id, $tiendasAsignadas) ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                >
                                <span class="ml-3 text-sm text-gray-700">
                                    <span class="font-medium">{{ $tienda->cr }}</span>
                                    @if($tienda->tienda)
                                        <span class="text-gray-500"> - {{ $tienda->tienda }}</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ $tiendas->count() }} tienda(s) disponible(s) en esta plaza
                    </p>
                </div>
            @else
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        No hay tiendas registradas para la plaza <strong>{{ $user->plaza }}</strong>.
                        Las tiendas se crean automáticamente al importar archivos MAF.
                    </p>
                </div>
            @endif

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.tienda-assignment.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Guardar Asignación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection









