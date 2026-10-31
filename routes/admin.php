<?php

use App\Http\Controllers\Admin\AsistenciaController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\IntegranteController;
use App\Http\Controllers\Admin\JuegoController;
use App\Http\Controllers\Admin\SemestreController;
use App\Http\Controllers\Admin\SolicitudConvalidacionController;
use App\Http\Controllers\Admin\TorneoController;
use App\Http\Controllers\Admin\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', fn () => redirect()->route('admin.juegos.index'))->name('dashboard');

    Route::resource('juegos', JuegoController::class)->except(['show']);
    Route::resource('integrantes', IntegranteController::class)->except(['show']);
    Route::resource('torneos', TorneoController::class)->except(['show']);
    Route::resource('semestres', SemestreController::class)->except(['show']);

    Route::get('convalidaciones/exportar', [SolicitudConvalidacionController::class, 'exportar'])
        ->name('convalidaciones.exportar');
    Route::get('convalidaciones', [SolicitudConvalidacionController::class, 'index'])->name('convalidaciones.index');
    Route::get('convalidaciones/{convalidacion}/edit', [SolicitudConvalidacionController::class, 'edit'])->name('convalidaciones.edit');
    Route::put('convalidaciones/{convalidacion}', [SolicitudConvalidacionController::class, 'update'])->name('convalidaciones.update');

    Route::get('asistencia/exportar', [AsistenciaController::class, 'exportar'])->name('asistencia.exportar');
    Route::get('asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');

    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
});
