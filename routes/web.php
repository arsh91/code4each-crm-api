<?php

use App\Http\Controllers\Web\ComponentController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\ComponentAreaController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

//Show Login at Home
Route::get('/', [LoginController::class, 'show'])->name('login');
Route::match(['get', 'post'], '/login', [LoginController::class, 'login'])->name('login.user');

//Protect Routes With Auth
Route::post('/saveArea',[ComponentAreaController::class,'saveArea']);
Route::post('/componentareas/updateArea',[ComponentAreaController::class,'updateArea']);

Route::group(['middleware' => ['auth']], function() {
    //Dashboard Controller
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard.index');


    //Component Section Routes
    Route::get('/components',[ComponentController::class,'index'])->name('components.index');
    Route::get('/components/create',[ComponentController::class,'create'])->name('components.create');
    Route::post('/components',[ComponentController::class,'store'])->name('components.store');
    Route::get('/components/edit/{id}',[ComponentController::class,'edit'])->name('components.edit');
    Route::post('/components/{id}',[ComponentController::class,'update'])->name('components.update');
    
    
    Route::get('/componentareas/{id}',[ComponentAreaController::class,'index'])->name('componentareas.index');
    Route::get('/componentareas/create/{id}',[ComponentAreaController::class,'create'])->name('componentareas.create');
    Route::get('/componentareas/edit/{componentId}/{id}',[ComponentAreaController::class,'edit'])->name('componentareas.edit');
    Route::get('/componentareas/addfields/{id}/{componentId}',[ComponentAreaController::class,'addfields'])->name('componentareas.addfields');
    Route::post('/componentareas/savefields/{id}/{componentId}',[ComponentAreaController::class,'savefields'])->name('componentareas.savefields');
    Route::get('/componentareas/editfields/{componentId}/{componentAreaId}',[ComponentAreaController::class,'editfields'])->name('componentareas.editfields');
    Route::post('/componentareas/updatefields/{componentId}/{componentAreaId}',[ComponentAreaController::class,'updatefields'])->name('componentareas.updatefields');
    
    //save area with fields
    Route::post('/componentareas/saveareafields/{componentId}',[ComponentAreaController::class,'saveareafields'])->name('componentareas.saveareafields');
    Route::post('/componentareas/updateareafields/{componentId}/{componentAreaId}',[ComponentAreaController::class,'updateareafields'])->name('componentareas.updateareafields');
    
    Route::delete('/componentareas/destroy/{componentId}/{componentAreaId}',[ComponentAreaController::class,'destroy'])->name('componentareas.destroy');


	Route::get('logout', [LoginController::class, 'logOut'])->name('logout');

});



//clear cache
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('clear-all');
    return "Cache cleared successfully!";
});

//migrate new migration with url hit
Route::get('/migrate', function () {
    $exitCode = Artisan::call('migrate');
    return "Migration command executed successfully!";
});
