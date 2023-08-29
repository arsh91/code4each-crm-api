<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register',[RegistrationController::class,'store']);
Route::post('/login',[RegistrationController::class,'Login'])->name('login');
Route::get('email/verify/{id}',[VerificationController::class,'verify'])->name('verification.verify');
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('email/resend',[VerificationController::class,'resend'])->name('verification.resend');
    Route::post('/logout',[RegistrationController::class,'logout']);
    Route::get('/dashbaord',[DashboardController::class,'index'])->middleware('verifiedEmail');  
    Route::middleware('verified')->group(function () {
    Route::get('/profile',[ProfileController::class,'index']);  
    });
});


