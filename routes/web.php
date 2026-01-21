<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MafImportController;
use App\Http\Controllers\MafBatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminTiendaAssignmentController;
use App\Http\Controllers\Admin\AdminCategoriaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\InventarioPSFController;
use App\Http\Controllers\MovimientoEquiposController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Dashboard / Inicio
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('module:dashboard')->name('dashboard');
    Route::post('/dashboard', [DashboardController::class, 'search'])->middleware('module:dashboard')->name('dashboard.search');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // MAF Import
    Route::get('/maf/import', [MafImportController::class, 'create'])->name('maf.import.create');
    Route::post('/maf/import', [MafImportController::class, 'store'])->name('maf.import.store');

    // MAF Batches
    Route::get('/maf/batches', [MafBatchController::class, 'index'])->name('maf.batches.index');
    Route::get('/maf/batches/{batch}', [MafBatchController::class, 'show'])->name('maf.batches.show');
    Route::get('/maf/batches/{batch}/report.csv', [MafBatchController::class, 'exportCsv'])->name('maf.batches.export');
    Route::post('/maf/batches/{batch}/apply-categories', [MafBatchController::class, 'applyCategories'])->name('maf.batches.apply-categories')->middleware('can:admin');

    // Redirigir rutas antiguas de search a dashboard
    Route::get('/maf/search', function () {
        return redirect()->route('dashboard');
    });
    Route::post('/maf/search', [DashboardController::class, 'search'])->name('maf.search');

    // Inventario
    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::get('/captura', [InventarioController::class, 'captura'])->middleware('module:inventario.captura')->name('captura');
        Route::get('/consulta', [InventarioController::class, 'consulta'])->middleware('module:inventario.consulta')->name('consulta');
        Route::get('/realizados', [InventarioController::class, 'realizados'])->middleware('module:inventario.realizados')->name('realizados');
        Route::post('/buscar-tienda', [InventarioController::class, 'buscarTienda'])->middleware('module:inventario.captura')->name('buscar-tienda');
        Route::post('/obtener-equipos', [InventarioController::class, 'obtenerEquipos'])->middleware('module:inventario.captura')->name('obtener-equipos');
        Route::post('/guardar', [InventarioController::class, 'guardarInventario'])->middleware('module:inventario.captura')->name('guardar');
        Route::post('/buscar-tienda-consulta', [InventarioController::class, 'buscarTiendaConsulta'])->middleware('module:inventario.consulta')->name('buscar-tienda-consulta');
        Route::post('/obtener-equipos-consulta', [InventarioController::class, 'obtenerEquiposConsulta'])->middleware('module:inventario.consulta')->name('obtener-equipos-consulta');
        Route::post('/obtener-tiendas-realizados', [InventarioController::class, 'obtenerTiendasRealizados'])->middleware('module:inventario.realizados')->name('obtener-tiendas-realizados');
    });

    // Inventario PSF
    Route::prefix('inventario-psf')->name('inventario-psf.')->group(function () {
        Route::get('/captura', [InventarioPSFController::class, 'captura'])->middleware('module:inventario-psf.captura')->name('captura');
        Route::get('/consulta', [InventarioPSFController::class, 'consulta'])->middleware('module:inventario-psf.consulta')->name('consulta');
        Route::get('/obtener-equipos', [InventarioPSFController::class, 'obtenerEquipos'])->middleware('module:inventario-psf.consulta')->name('obtener-equipos');
        Route::get('/movimientos', [InventarioPSFController::class, 'movimientos'])->middleware('module:inventario-psf.movimientos')->name('movimientos');
        Route::post('/buscar-equipo', [InventarioPSFController::class, 'buscarEquipo'])->middleware('module:inventario-psf.captura')->name('buscar-equipo');
        Route::post('/guardar', [InventarioPSFController::class, 'guardar'])->middleware('module:inventario-psf.captura')->name('guardar');
        Route::put('/{inventario}/ubicacion', [InventarioPSFController::class, 'actualizarUbicacion'])->middleware('module:inventario-psf.consulta')->name('actualizar-ubicacion');
        Route::post('/{inventario}/dar-de-baja', [InventarioPSFController::class, 'darDeBaja'])->middleware('module:inventario-psf.consulta')->name('dar-de-baja');
    });

    // Movimientos de Equipos
    Route::prefix('movimientos')->name('movimientos.')->group(function () {
        Route::get('/', [MovimientoEquiposController::class, 'index'])->middleware('module:movimientos.index')->name('index');
        Route::get('/consulta', [MovimientoEquiposController::class, 'consulta'])->middleware('module:movimientos.consulta')->name('consulta');
        Route::get('/{movimiento}/detalle', [MovimientoEquiposController::class, 'detalle'])->middleware('module:movimientos.consulta')->name('detalle');
        Route::post('/buscar-equipo', [MovimientoEquiposController::class, 'buscarEquipo'])->middleware('module:movimientos.index')->name('buscar-equipo');
        Route::post('/buscar-equipo-remplazo', [MovimientoEquiposController::class, 'buscarEquipoRemplazo'])->middleware('module:movimientos.index')->name('buscar-equipo-remplazo');
        Route::post('/buscar-tienda', [MovimientoEquiposController::class, 'buscarTienda'])->middleware('module:movimientos.index')->name('buscar-tienda');
        Route::post('/validar-inventario', [MovimientoEquiposController::class, 'validarInventario'])->middleware('module:movimientos.index')->name('validar-inventario');
        Route::post('/guardar', [MovimientoEquiposController::class, 'guardarMovimiento'])->middleware('module:movimientos.index')->name('guardar');
    });

    // Admin - Gestión de Usuarios y Asignación de Tiendas
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::get('tienda-assignment', [AdminTiendaAssignmentController::class, 'index'])->name('tienda-assignment.index');
        Route::get('tienda-assignment/{user}/edit', [AdminTiendaAssignmentController::class, 'edit'])->name('tienda-assignment.edit');
        Route::put('tienda-assignment/{user}', [AdminTiendaAssignmentController::class, 'update'])->name('tienda-assignment.update');
        
        // Catálogo de Categorías
        Route::resource('categorias', AdminCategoriaController::class);
        Route::get('categorias/import', [AdminCategoriaController::class, 'showImport'])->name('categorias.import');
        Route::post('categorias/import', [AdminCategoriaController::class, 'import'])->name('categorias.import.store');
    });
});

require __DIR__.'/auth.php';






