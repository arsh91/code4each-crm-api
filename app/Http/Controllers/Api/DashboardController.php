<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Undefined;

class DashboardController extends Controller
{

    public function index() 
    {
        $response = [
            'success' => true,
            'status' => 200,
        ];
        if (session()->has('verification_notice')) {
            $response['notification'] = session('verification_notice');
            $response['resend-link'] = route('verification.resend');
            session()->forget('verification_notice');
        }
        $user = User::with('agency')->where('id',auth()->user()->id)->first();
        $response['user'] = $user;
         $response['message'] =  "Welcome to the dashboard.";
        return response()->json($response);
    }
}
