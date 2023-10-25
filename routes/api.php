<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComponentsControllers;
use App\Http\Controllers\Api\ComponentSettingsController;
use App\Http\Controllers\Api\CustomizeComponentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FeedBackController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WebsiteSettingsController;
use App\Http\Controllers\Api\WordpressComponentController;

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
//Authenticated Group Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/dashboard',[DashboardController::class,'index'])->middleware('verifiedEmail');
    Route::post('/feedback',[FeedBackController::class,'feedback']);
    Route::middleware('throttle:3,20')->get('email/resend',[VerificationController::class,'resend'])->name('verification.resend');
    // Route::post('/forgot-password',[AuthController::class,'forgotPassword']);
    Route::post('/logout',[AuthController::class,'logout']);

    //Email Verified Routes
    Route::middleware('verified')->group(function () {
    Route::post('/agency-website-details',[ComponentsControllers::class,'agencyWebsiteDetails']);
    // Route::get('/agency-website-info/{agency_id}',[DashboardController::class,'getAgencyWebsiteInfo']);
    Route::get('/get-website-categories',[DashboardController::class,'getWebsiteCategories']);
    Route::post('store-components', [ComponentsControllers::class, 'sendComponentToWordpress' ]);
    Route::post('components/regenerate', [ComponentsControllers::class, 'regenerateComponents' ]);
    Route::get('/get-active-components',[ComponentsControllers::class,'getActiveWordpressComponents']);
    Route::get('/fetch-active-components-detail',[ComponentsControllers::class,'fetchActiveComponentsDetail']);
    Route::get('/get-components-global-colors',[ComponentsControllers::class,'getWordpressGlobalColors']);
    Route::post('/update-global-colors',[ComponentsControllers::class,'updateWordpressGlobalColors']);
    Route::post('/add-global-colors',[ComponentsControllers::class,'addWordpressGlobalColors']);
    Route::get('/fetch-components',[CustomizeComponentController::class,'fetchComponent']);
    Route::get('/color-combinations',[CustomizeComponentController::class,'getColorCombination']);
    Route::post('/update-color-combination',[CustomizeComponentController::class,'updateColorCombination']);
    Route::get('/get-fonts',[CustomizeComponentController::class,'getFont']);
    Route::post('/change-component',[CustomizeComponentController::class,'updateComponent']);
    Route::post('/change-font-family',[CustomizeComponentController::class,'updateFont']);
    Route::get('settings',[WebsiteSettingsController::class,'settings']);
    });
});


