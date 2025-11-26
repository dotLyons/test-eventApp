<?php

use App\Http\Middleware\CheckAdminPin;
use App\Livewire\AdminLogin;
use App\Livewire\CashierDashboard;
use App\Livewire\GuestMenu;
use App\Livewire\KitchenDashboard;
use App\Livewire\ProductManager;
use App\Livewire\TableManager;
use Illuminate\Support\Facades\Route;

// --- RUTA PÚBLICA 1: Login ---
// Esta es la puerta de entrada para el personal
Route::get('/', AdminLogin::class)->name('login.pin');

// --- RUTA PÚBLICA 2: Menú Invitados ---
// ¡Esta NO debe tener clave! Los invitados entran directo con el QR.
Route::get('/menu/{uuid}', GuestMenu::class)->name('guest.menu');

// --- RUTAS PROTEGIDAS Requieren PIN ---
Route::middleware(CheckAdminPin::class)->group(function () {

    Route::get('/dashboard', KitchenDashboard::class)->name('dashboard');
    Route::get('/productos', ProductManager::class)->name('products');
    Route::get('/caja', CashierDashboard::class)->name('cashier');
    Route::get('/mesas', TableManager::class)->name('tables');

    // Ruta para salir (Logout)
    Route::get('/salir', function () {
        session()->forget('admin_access');

        return redirect()->route('login.pin');
    })->name('logout');
});
