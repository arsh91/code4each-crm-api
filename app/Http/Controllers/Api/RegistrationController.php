<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\User;
use App\Notifications\CommonEmailNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator; 
use Laravel\Passport\PersonalAccessTokenResult;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $validate = $validator->valid();
        $agencyObj = new Agency();
        $agencyObj->name = $validate['company_name'];
        if(!empty($validate['description']))
        {
            $agencyObj->description = $validate['description'];
        }
        $agencyObj->save();
        $agency_id = $agencyObj->id;
        if($agencyObj->save()){
            $userObj = new User();
            $userObj->agency_id = $agency_id;
            $userObj->name = $validate['name'];
            $userObj->email = $validate['email'];
            $userObj->phone = $validate['phone'];
            $userObj->password = Hash::make($validate['password']);
            $userObj->save();
        }
        if($userObj->save()){
            // Generate an API token for the user
            $token = $userObj->createToken('api')->accessToken;

            // $userObj->sendEmailVerificationNotification();
            // Generate the email verification link
            // event(new Registered($userObj = $this->create($request)));
            $verificationLink = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                ['id' => $userObj->id, 'hash' => sha1($userObj->getEmailForVerification())]
            );
            $messages = [
                'subject' => 'Confirmation Email for Registering On Code4Each CRM Portal',
                'additional-info' => 'If you have Already Verified Your Account, please ignore this email. Your account will not be activated unless you confirm your email address.',
                'url-title' => 'Verify Email Address',
                'url' => $verificationLink,
                'lines_array' => [
                'title' => 'Congratulations and welcome to Code4Each CRM! We \'re thrilled to have you as a new member of our community.',
                'body-text' => 'To get started, please click on the link below to confirm your email address and activate your account:',
                ],
            ];
            $userObj->notify(new CommonEmailNotification($messages));
            $messages = [
                'subject' => 'New Agency Is Register With Our CRM Platform',
                'url-title' => 'Find Detail',
                'url' => '/',
                'lines_array' => [
                'title' => 'Dear Admin,',
                'body-text' => 'We have found that New Agency Is Register With Us. Please Find Detail Below:',
                'special_Agency_Name' => $agencyObj->name,
                'special_Email' => $userObj->email,
                ],
            ];
             (new User)->forceFill([
                'name' => 'Admin',
                'email' => 'admin.code4each@yopmail.com',
            ])->notify(new CommonEmailNotification($messages));
            
           
        }

        return response()->json([
            'message' => 'Company Register Successfully.',
            // 'agency data' => $agencyObj,
            // 'user data' => $userObj,
            'token' => $token,
            'status' => 200,
        ]);
    }


    public function login(Request $request)
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $tokenResult = $user->createToken('API Token');
            $token = $tokenResult->accessToken;

            $response['success'] = true;
            $response['status'] = 200;
            $response['message'] = 'User Login Successfully';
            $response['token'] = $token;

            return response()->json($response);
        } else {
            $response['message'] = 'Invalid credentials';
            $response['status'] = 401;

            return response()->json($response);
        }
    }
        public function logout(Request $request)
        {
            $user = Auth::user();
            $user->tokens()->delete(); // Revoke all user's tokens

            $response = [
                'success' => true,
                'status' => 200,
                'message' => 'User logged out successfully.',
            ];

            return response()->json($response);
        }

}
