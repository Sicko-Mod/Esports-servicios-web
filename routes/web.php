<?php

use App\Http\Controllers\SitioController;
use Illuminate\Support\Facades\Route;

Route::controller(SitioController::class)->group(function () {
    Route::get('/', 'inicio')->name('inicio');
    Route::get('/equipos', 'equipos')->name('equipos.index');
    Route::get('/equipos/{juego:slug}', 'equipo')->name('equipos.show');
    Route::get('/torneos', 'torneos')->name('torneos.index');
});

Route::view('/convalidacion-de-creditos', 'paginas.convalidacion')->name('convalidacion');
Route::view('/como-unirse', 'paginas.como-unirse')->name('como-unirse');
Route::view('/contacto', 'paginas.contacto')->name('contacto');
Route::view('/politica-de-privacidad', 'paginas.privacidad')->name('privacidad');
