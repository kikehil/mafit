@extends('layouts.app')

@section('title', 'Nueva Categoría')
@section('page-title', 'Nueva Categoría')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Nueva Categoría</h2>

        <form method="POST" action="{{ route('admin.categorias.store') }}">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="descripcion_raw" class="block text-sm font-medium text-gray-700">
                        Descripción <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="descripcion_raw" 
                        name="descripcion_raw" 
                        value="{{ old('descripcion_raw') }}"
                        required
                        maxlength="255"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Ej: SWITCH RED LOCAL"
                    >
                    <p class="mt-1 text-sm text-gray-500">La descripción se normalizará automáticamente para evitar duplicados.</p>
                    @error('descripcion_raw')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="categoria" class="block text-sm font-medium text-gray-700">
                        Categoría <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="categoria" 
                        name="categoria" 
                        value="{{ old('categoria') }}"
                        required
                        maxlength="80"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Ej: CCTV, ENERGIA, MOVILIDAD, PUNTO DE VENTA, TELCO"
                    >
                    @error('categoria')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="activo" 
                            value="1"
                            {{ old('activo', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                        <span class="ml-2 text-sm text-gray-700">Activo</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Guardar
                </button>
                <a 
                    href="{{ route('admin.categorias.index') }}" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection








