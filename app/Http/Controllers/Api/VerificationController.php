<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired URL provided."], 401);
        }
    
        $user = User::findOrFail($user_id);
    
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    
        $response = [
            'success' => true,
            'status' => 200,
            'message' => 'Email verified successfully.',
        ];
    
        return response()->json($response);
    }
    
    public function resend() {
        
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }
    
        auth()->user()->sendEmailVerificationNotification();
    
        $response = [
            'success' => true,
            'status' => 200,
            'message' => 'Email verification link sent on your email id.',
        ];
    
        return response()->json($response);
    }
}
