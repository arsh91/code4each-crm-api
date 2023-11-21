<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreBooking;


class PreBookingController extends Controller
{
    public function saveEmailForPreBooking(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:pre_booking,email',
        ]);

        PreBooking::create([
            'email' => $request->input('email'),
        ]);

        $count = PreBooking::count();
        return response()->json([
            'message' => 'Email inserted successfully',
            'data' => ['count' => $count],
        ], 201);
    }

}
