@extends('layouts.app')

@section('title', 'Crear Usuario')
@section('page-title', 'Crear Usuario')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Nuevo Usuario</h2>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}" 
                        required 
                        maxlength="120"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        maxlength="190"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone') }}" 
                        maxlength="30"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plaza -->
                <div>
                    <label for="plaza" class="block text-sm font-medium text-gray-700">Plaza</label>
                    <select 
                        name="plaza" 
                        id="plaza" 
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('plaza') border-red-500 @enderror"
                    >
                        <option value="">Seleccione una plaza</option>
                        @foreach($plazas as $plaza)
                            <option value="{{ $plaza->plaza }}" {{ old('plaza') == $plaza->plaza ? 'selected' : '' }}>
                                {{ $plaza->plaza }} - {{ $plaza->plaza_nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('plaza')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rol -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                    <select 
                        name="role" 
                        id="role" 
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror"
                    >
                        <option value="supervisor" {{ old('role', 'supervisor') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="tecnico" {{ old('role') == 'tecnico' ? 'selected' : '' }}>Técnico</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        minlength="8"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required 
                        minlength="8"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>
            </div>

            <!-- Módulos -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Permisos de Módulos</label>
                <p class="text-sm text-gray-500 mb-4">Seleccione los módulos a los que el usuario tendrá acceso. Los administradores tienen acceso a todos los módulos.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 border border-gray-200 rounded-lg p-4">
                    @foreach($modules as $module)
                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                        <input 
                            type="checkbox" 
                            name="modules[]" 
                            value="{{ $module->id }}"
                            {{ old('modules') && in_array($module->id, old('modules')) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="text-sm text-gray-700">{{ $module->display_name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('modules')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



