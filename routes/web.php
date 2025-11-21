<?php

use App\Livewire\GuestMenu;
use App\Livewire\KitchenDashboard;
use App\Livewire\ProductManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', KitchenDashboard::class)->name('dashboard');

Route::get('/productos', ProductManager::class)->name('products');

Route::get('/menu/{uuid}', GuestMenu::class)->name('guest.menu');