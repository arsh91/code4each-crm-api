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
            'user_id' => 'nullable',
            'website_id' => 'nullable',
            'agency_id' => 'nullable',
            'type' => 'required',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'rating' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validate = $validator->valid();

        $feedbackObj = new Feedback();
        $feedbackObj->user_id       = $validate['user_id'] ?? null;
        $feedbackObj->website_id    = $validate['website_id'] ?? null;
        $feedbackObj->agency_id     = $validate['agency_id'] ?? null;
        $feedbackObj->type          = $validate['type'];
        $feedbackObj->name          = $validate['name'] ?? null;
        $feedbackObj->email         = $validate['email'] ?? null;
        $feedbackObj->phone         = $validate['phone'] ?? null;
        $feedbackObj->title       = $validate['title'];
        $feedbackObj->message       = $validate['message'];
        $feedbackObj->rating       = $validate['rating'] ?? null;
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
