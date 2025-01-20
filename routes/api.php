<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComponentsControllers;
use App\Http\Controllers\Api\ComponentSettingsController;
use App\Http\Controllers\Api\CustomComponentFieldsController;
use App\Http\Controllers\Api\CustomizeComponentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeleteWebsiteController;
use App\Http\Controllers\Api\FeedBackController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\GoogleSocialiteController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\OtpVerificationControlller;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\WebsiteSettingsController;
use App\Http\Controllers\Api\WordpressComponentController;
use App\Http\Controllers\Api\PreBookingController;
use App\Http\Controllers\Api\CustomSectionsController;
use Google\Service\Monitoring\Custom;
use App\Http\Controllers\Api\WordpressMenusController;
use App\Http\Controllers\Api\SubscriptionPaymentController;



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
Route::get('payment',[SubscriptionPaymentController::class,'processPayment']);
Route::post('/register',[RegistrationController::class,'store']);
Route::post('/login',[AuthController::class,'Login'])->name('login');

// Route::get('auth/google', [GoogleSocialiteController::class, 'redirectToGoogle']);
// Route::get('/auth/google/callback', [GoogleSocialiteController::class, 'handleCallback']);
Route::get('/auth/google/register', [GoogleSocialiteController::class, 'handleGoogleLogin']);

Route::get('email/verify/{id}',[VerificationController::class,'verify'])->name('verification.verify');
Route::post('/forgot-password',[ForgotPasswordController::class,'forgotPassword']);
Route::post('/reset-password',[ResetPasswordController::class,'resetPassword']);

//Authenticated Group Routes
Route::middleware('auth:api')->group(function () {
    //Unverified Routes
   
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/dashboard',[DashboardController::class,'index'])->middleware('verifiedEmail');
    Route::post('/update-left-fields',[GoogleSocialiteController::class,'updateLeftFields']);
    Route::middleware('throttle:3,20')->get('email/resend',[VerificationController::class,'resend'])->name('verification.resend');
    //Delete Website On User Request
    Route::post('/get-otp',[OtpVerificationControlller::class,'generateOtp']);
    Route::post('/verify-otp',[OtpVerificationControlller::class,'verifyOtp']);
    Route::post('/delete-website',[DeleteWebsiteController::class,'deleteWebsite']);
    Route::post('/logout',[AuthController::class,'logout']);
    
    Route::post('subscriptionPayment',[SubscriptionPaymentController::class,'subscriptionPayment']);
    Route::get('get-plans',[SubscriptionPaymentController::class,'fetchplans']);
    Route::post('create-order',[SubscriptionPaymentController::class,'createOrder']);
    //End Unverified Routes

    //Email Verified Routes
    Route::middleware('verified')->group(function () {
    Route::post('/agency-website-details',[ComponentsControllers::class,'agencyWebsiteDetails']);
    Route::get('/agency-website-info/{agency_id}',[DashboardController::class,'getAgencyWebsiteInfo']);
    Route::get('/get-website-categories',[DashboardController::class,'getWebsiteCategories']);

    Route::post('store-components', [ComponentsControllers::class, 'sendComponentToWordpress' ]);
    Route::post('components/regenerate', [ComponentsControllers::class, 'regenerateComponents' ]);
    Route::get('/get-active-components',[ComponentsControllers::class,'getActiveWordpressComponents']);
    Route::get('/fetch-active-components-detail',[ComponentsControllers::class,'fetchActiveComponentsDetail']);
    Route::get('/get-components-global-colors',[ComponentsControllers::class,'getWordpressGlobalColors']);
    Route::post('/update-global-colors',[ComponentsControllers::class,'updateWordpressGlobalColors']);
    Route::post('/add-global-colors',[ComponentsControllers::class,'addWordpressGlobalColors']);
    Route::get('/get-components',[ComponentsControllers::class,'getComponents']);

    Route::get('/fetch-components',[CustomizeComponentController::class,'fetchComponent']);
    Route::get('/color-combinations',[CustomizeComponentController::class,'getColorCombination']);
    Route::post('/update-color-combination',[CustomizeComponentController::class,'updateColorCombination']);
    Route::get('/get-fonts',[CustomizeComponentController::class,'getFont']);
    Route::post('/change-component',[CustomizeComponentController::class,'updateComponent']);
    Route::post('/change-font-family',[CustomizeComponentController::class,'updateFont']);
    Route::post('update-component-position',[CustomizeComponentController::class,'updateComponentPosition']);

    Route::post('/add-new-section',[CustomSectionsController::class,'addComponentSection']);
    

    Route::get('settings',[WebsiteSettingsController::class,'settings']);
    Route::post('/update-settings',[WebsiteSettingsController::class,'updateSettings']);

    Route::get('get-component-form-fields',[CustomComponentFieldsController::class,'getFormFields']);
    Route::post('update-component-form-fields',[CustomComponentFieldsController::class,'updateComponentFormFields']);
    Route::get('/get-social-links',[CustomComponentFieldsController::class,'getSocialLinks']);
    Route::post('/update-social-links',[CustomComponentFieldsController::class,'updateSocialLinks']);

    Route::get('/get-site-menus',[WordpressMenusController::class,'getWordpressMenus']);
    Route::post('/add-site-menus',[WordpressMenusController::class,'postWordpressMenus']);
    Route::post('/update-site-menu',[WordpressMenusController::class,'updateWordpressMenu']);
    Route::delete('/delete-site-menu',[WordpressMenusController::class,'deleteWordpressMenu']);
    Route::post('/change-menu-position',[WordpressMenusController::class,'changeMenuPosition']);


    Route::post('upload-images',[ImageController::class,'uploadImages']);
    Route::get('uploaded-images',[ImageController::class,'getComponentImages']);
    Route::delete('delete-uploaded-images',[ImageController::class,'deleteUploadedImages']);

    Route::get('/website-templates', [ComponentsControllers::class, 'getWebsiteTemplates']);

    });
    //End of Verified Routes
  
   
});
//End of Authenticated Group Routes


Route::post('/feedback',[FeedBackController::class,'feedback']);
Route::post('pre-booking', [PreBookingController::class,'saveEmailForPreBooking']);

//------------





