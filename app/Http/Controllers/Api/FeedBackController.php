<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedBackController extends Controller
{
    public function feedback(Request $request)
    {
        $response = [
            "status" => 400,
            "success" => true,
        ];

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type' => 'required',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validate = $validator->valid();

        $feedbackObj = new Feedback();
        $feedbackObj->user_id = $validate['user_id'];
        $feedbackObj->type = $validate['type'];
        $feedbackObj->message = $validate['message'];
        $feedbackObj->save();
        if($feedbackObj){
            $response = [
                "message" => "FeedBack Saved Successfully.",
                "status" => 200,
                "success" => true,
            ];
        }

        return response()->json($response);
    }
}
