<?php

use App\Http\Controllers\CrowfondingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CrowfondingController::class, 'home'])->name('home');
Route::view('/terminos-y-condiciones', 'crow.terms')->name('terms');
Route::get('/media/{type}/{filename}', [CrowfondingController::class, 'privateMedia'])
    ->whereIn('type', ['avatars', 'products'])
    ->where('filename', '[A-Fa-f0-9]{32}\\.(?:jpg|jpeg|png|webp)')
    ->name('media.private');
Route::get('/equipos/{product}', [CrowfondingController::class, 'product'])->name('products.show');
Route::get('/componentes', [CrowfondingController::class, 'components'])->name('components.index');
Route::get('/componentes/{componentCategory:slug}', [CrowfondingController::class, 'componentCategory'])->name('components.show');
Route::get('/servicios', [CrowfondingController::class, 'services'])->name('services');
$authenticatedMiddleware = app()->isProduction() ? ['auth', 'verified'] : ['auth'];
Route::post('/servicios', [CrowfondingController::class, 'storeTicket'])->middleware($authenticatedMiddleware)->name('tickets.store');
Route::middleware($authenticatedMiddleware)->group(function () {
    Route::get('/mi-cuenta', [CrowfondingController::class, 'dashboard'])->name('dashboard');
    Route::get('/perfil', [CrowfondingController::class, 'profile'])->name('profile');
    Route::put('/perfil', [CrowfondingController::class, 'updateProfile'])->name('profile.update');
    Route::put('/perfil/contraseña', [CrowfondingController::class, 'updatePassword'])->name('profile.password');
    Route::get('/administracion', [CrowfondingController::class, 'admin'])->middleware('admin')->name('admin');
    Route::post('/administracion/equipos', [CrowfondingController::class, 'storeProduct'])->middleware('admin')->name('admin.products.store');
    Route::delete('/administracion/equipos/{product}', [CrowfondingController::class, 'deleteProduct'])->middleware('admin')->name('admin.products.delete');
    Route::post('/administracion/categorias-componentes', [CrowfondingController::class, 'storeComponentCategory'])->middleware('admin')->name('admin.component-categories.store');
    Route::delete('/administracion/categorias-componentes/{componentCategory:slug}', [CrowfondingController::class, 'deleteComponentCategory'])->middleware('admin')->name('admin.component-categories.delete');
    Route::patch('/administracion/tickets/{ticket}', [CrowfondingController::class, 'updateTicket'])->middleware('admin')->name('admin.tickets.update');
    Route::post('/administracion/usuarios', [CrowfondingController::class, 'storeUser'])->middleware('admin')->name('admin.users.store');
    Route::patch('/administracion/usuarios/{user}', [CrowfondingController::class, 'updateUserRole'])->middleware('admin')->name('admin.users.role');
});

require __DIR__.'/settings.php';
