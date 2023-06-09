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
Route::get('/factura', [App\Http\Controllers\FacturaController::class, 'index'])->name('factura');
Route::get('/ejercicio1', [App\Http\Controllers\Ejercicio1Controller::class, 'index'])->name('ejercicio1');
Route::get('/ep1', [App\Http\Controllers\Ep1Controller::class, 'index'])->name('ep1');
Route::get('/ep2', [App\Http\Controllers\Ep2Controller::class, 'index'])->name('ep2');
