<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleSocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleCallback(Request $request)
    {
        try {
           $user = Socialite::driver('google')->stateless()->user();
            $findUser = User::where('social_id', $user->id)->first();

            if ($findUser) {
                $token = $findUser->createToken('access-token')->accessToken;

                return response()->json(['message' => 'User logged in successfully', 'user' => $findUser, 'access_token' => $token]);
            } else {
                $agencyObj = new Agency();
                $agencyObj->name = 'Agency Name';
                $agencyObj->save();
                $agencyId = $agencyObj->id;

                if ($agencyObj) {
                    $userObj = new User();
                    $userObj->name = $user->name;
                    $userObj->email = $user->email;
                    $userObj->phone = 'N/A'; // Adjust as needed
                    $userObj->agency_id = $agencyId;
                    $userObj->social_id = $user->id;
                    $userObj->social_type = 'google';
                    $userObj->email_verified_at = $user['email_verified'] ? now() : null;
                    $userObj->role = 'admin';
                    $userObj->password = bcrypt('my-google');
                    $userObj->save();

                    $token = $userObj->createToken('access-token')->accessToken;

                    return response()->json(['message' => 'User registered and logged in successfully', 'user' => $userObj, 'access_token' => $token]);
                }
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            return response()->json(['error' => 'error While Logging in with Google'], 500);
        }
    }
}
