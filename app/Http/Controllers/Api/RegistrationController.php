<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\User;
use App\Notifications\CommonEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        // dd($request);
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
        // dd($validate);
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
            $messages["subject"] = "Confirmation Email for Registering On Code4Each CRM Portal";
            $messages["title"] = "Congratulations and welcome to Code4Each CRM! We're thrilled to have you as a new member of our community.";
            $messages['body-text'] = "To get started, please click on the link below to confirm your email address and activate your account:";
            $messages['additional-info'] = "If you have Already Verified Your Account, please ignore this email. Your account will not be activated unless you confirm your email address.";
            $messages['url-title'] = "Verify Email";
            $messages['url'] = "/";
            $userObj->notify(new CommonEmailNotification($messages));
        }

        return response()->json([
            'message' => 'Company Register Successfully.',
            'agency data' => $agencyObj,
            'user data' => $userObj,
        ]);
    }


    public function getUser()
    {
        dd("getuser");
    }
}
