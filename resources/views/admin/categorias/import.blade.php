@extends('layouts.app')

@section('title', 'Importar Catálogo de Categorías')
@section('page-title', 'Importar Catálogo de Categorías')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Importar Catálogo de Categorías</h2>

        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">Formato del archivo:</h3>
            <ul class="text-sm text-blue-800 list-disc list-inside space-y-1">
                <li>Archivos soportados: .xlsx, .xls, .csv</li>
                <li>Debe contener las columnas: <strong>descripcion</strong> y <strong>categoria</strong></li>
                <li>La primera fila debe ser el encabezado</li>
                <li>Las descripciones se normalizarán automáticamente</li>
                <li>Si una descripción ya existe, se actualizará en lugar de duplicarse</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('admin.categorias.import.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700">
                        Archivo <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="file" 
                        id="file" 
                        name="file" 
                        accept=".xlsx,.xls,.csv"
                        required
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                    >
                    <p class="mt-1 text-sm text-gray-500">Tamaño máximo: 10MB</p>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    Importar
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


