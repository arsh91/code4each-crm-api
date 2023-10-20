<?php

use App\Http\Controllers\Web\ComponentController;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/components',[ComponentController::class,'index'])->name('components.index');
Route::get('/components/create',[ComponentController::class,'create'])->name('components.create');
Route::post('/components',[ComponentController::class,'store'])->name('components.store');
Route::get('/components/edit/{id}',[ComponentController::class,'edit'])->name('components.edit');
Route::post('/components/{id}',[ComponentController::class,'update'])->name('components.update');
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('clear-all');
    return "Cache cleared successfully!";
});
