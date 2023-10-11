<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComponentsControllers;
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
Route::post('/login',[AuthController::class,'Login'])->name('login');
Route::get('email/verify/{id}',[VerificationController::class,'verify'])->name('verification.verify');
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::middleware('throttle:3,20')->get('email/resend',[VerificationController::class,'resend'])->name('verification.resend');
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/dashboard',[DashboardController::class,'index'])->middleware('verifiedEmail');


    Route::middleware('verified')->group(function () {
    // Route::post('/agency-details',[DashboardController::class,'agencyDetails']);
    Route::post('/agency-website-details',[ComponentsControllers::class,'agencyWebsiteDetails']);
    // Route::get('/agency-website-info/{agency_id}',[DashboardController::class,'getAgencyWebsiteInfo']);
    Route::get('/get-website-categories',[DashboardController::class,'getWebsiteCategories']);
    Route::get('/get-component',[ComponentsControllers::class,'getComponent']);
    Route::post('store-components', [ComponentsControllers::class, 'sendComponentToWordpress' ]);
    Route::post('components/regenerate', [ComponentsControllers::class, 'regenerateComponents' ]);
    Route::get('/get-active-components',[ComponentsControllers::class,'getActiveWordpressComponents']);
    Route::get('/get-components-global-colors',[ComponentsControllers::class,'getWordpressGlobalColors']);
    Route::post('/update-global-colors',[ComponentsControllers::class,'updateWordpressGlobalColors']);
    Route::post('/add-global-colors',[ComponentsControllers::class,'addWordpressGlobalColors']);

    });
});


