<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
         $response['message'] =  "Welcome to the dashboard.";
        return response()->json($response);
    }
}
