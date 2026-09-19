<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CanchaController;

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('canchas', [CanchaController::class, 'index'])->name('canchas.index');
    Route::post('canchas/crear', [CanchaController::class, 'create'])->name('canchas.create');
    Route::post('canchas', [CanchaController::class, 'store'])->name('canchas.store');
    Route::get('canchas/{cancha}', [CanchaController::class, 'show'])->name('canchas.show');
    Route::get('canchas/{cancha}/editar', [CanchaController::class, 'edit'])->name('canchas.edit');
    Route::put('canchas/{cancha}', [CanchaController::class, 'update'])->name('canchas.update');
    Route::delete('canchas/{cancha}', [CanchaController::class, 'destroy'])->name('canchas.destroy');
    Route::patch('canchas/{cancha}/activar', [CanchaController::class, 'activar'])->name('canchas.activar');
});