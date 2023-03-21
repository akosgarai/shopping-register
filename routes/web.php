<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/documents/uploads/{filename}', [App\Http\Controllers\ImageController::class, 'viewTemp'])->name('image.viewTemp');
Route::get('/documents/receipts/{filename}', [App\Http\Controllers\ImageController::class, 'viewReceipt'])->name('image.viewReceipt');

// the following routes are behind auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/address', App\Http\Livewire\Crud\AddressCrud::class)->name('address');
    Route::get('/item', App\Http\Livewire\Crud\ItemCrud::class)->name('item');
    Route::get('/company', App\Http\Livewire\Crud\CompanyCrud::class)->name('company');
    Route::get('/shop', App\Http\Livewire\Crud\ShopCrud::class)->name('shop');
    Route::get('/basket', App\Http\Livewire\Crud\BasketCrud::class)->name('basket');
    Route::get('/receipt-scan', App\Http\Livewire\ReceiptScan::class)->name('receipt-scan');
});
