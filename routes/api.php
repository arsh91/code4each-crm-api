<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\VerificationController;

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
Route::post('/login',[RegistrationController::class,'Login']);
Route::get('/get-agency',[RegistrationController::class,'getUser']);

Route::get('email/verify/{id}',[VerificationController::class,'verify'])->name('verification.verify'); // Make sure to keep this as your route name
Route::get('email/resend',[VerificationController::class,'resend'])->name('verification.resend');
Route::middleware('auth:api')->group(function () {
    Route::get('/protected-route', 'ApiController@protectedMethod');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    // Protected routes go here
});


