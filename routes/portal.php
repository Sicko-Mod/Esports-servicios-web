<?php

use App\Http\Controllers\Portal\Auth\LoginController;
use App\Http\Controllers\Portal\Auth\RegisterController;
use App\Http\Controllers\Portal\Auth\VerificacionCorreoController;
use App\Http\Controllers\Portal\Capitan\ListaAsistenciaController;
use App\Http\Controllers\Portal\Capitan\SesionController;
use App\Http\Controllers\Portal\ConvalidacionController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\MiAsistenciaController;
use App\Http\Controllers\Portal\PerfilController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
    Route::get('registro', [RegisterController::class, 'create'])->name('register');
    Route::post('registro', [RegisterController::class, 'store'])
        ->middleware('throttle:login')
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('verificar-correo', [VerificacionCorreoController::class, 'aviso'])->name('verification.notice');
    Route::get('verificar-correo/{id}/{hash}', [VerificacionCorreoController::class, 'verificar'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('verificar-correo/reenviar', [VerificacionCorreoController::class, 'reenviar'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::middleware('verified:portal.verification.notice')->group(function () {
        Route::get('perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
        Route::put('perfil', [PerfilController::class, 'update'])->name('perfil.update');

        Route::middleware('perfil.completo')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('mi-asistencia', [MiAsistenciaController::class, 'index'])->name('asistencia.index');
            Route::get('convalidacion', [ConvalidacionController::class, 'index'])->name('convalidacion.index');
            Route::post('convalidacion', [ConvalidacionController::class, 'store'])->name('convalidacion.store');

            Route::prefix('capitan')->name('capitan.')->group(function () {
                Route::get('/', [SesionController::class, 'index'])->name('sesiones.index');
                Route::post('sesiones', [SesionController::class, 'store'])->name('sesiones.store');
                Route::get('sesiones/{sesion}/lista', [ListaAsistenciaController::class, 'edit'])->name('lista.edit');
                Route::put('sesiones/{sesion}/lista', [ListaAsistenciaController::class, 'update'])->name('lista.update');
            });
        });
    });
});
