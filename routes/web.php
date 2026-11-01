<?php

use App\Http\Controllers\CanchaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'dashboard' : 'login');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::controller(CanchaController::class)->group(function () {
        Route::get('canchas', 'index')->name('canchas.index');
        Route::post('canchas', 'store')->name('canchas.store');
        Route::put('canchas/{cancha}', 'update')->name('canchas.update');
        Route::delete('canchas/{cancha}', 'destroy')->name('canchas.destroy');
        Route::delete('canchas/{cancha}/eliminar', 'eliminar')->name('canchas.eliminar');
        Route::patch('canchas/{cancha}/activar', 'activar')->name('canchas.activar');
    });

    Route::controller(ReservaController::class)->group(function () {
        Route::get('reservas', 'index')->name('reservas.index');
        Route::post('reservas', 'store')->name('reservas.store');
        Route::patch('reservas/{reserva}/estado', 'cambiarEstado')->name('reservas.cambiar-estado');
        Route::delete('reservas/{reserva}', 'destroy')->name('reservas.destroy');
    });
});
