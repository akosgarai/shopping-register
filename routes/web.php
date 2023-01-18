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
Route::get('/scan', [App\Http\Controllers\ScanController::class, 'index'])->name('scan');
Route::get('/receipts', [App\Http\Controllers\ReceiptController::class, 'index'])->name('receipts');
Route::get('/receipt/{id}', [App\Http\Controllers\ReceiptController::class, 'view'])->name('receipts.view');
Route::get('/address', App\Http\Livewire\AddressCrud::class)->name('address');
