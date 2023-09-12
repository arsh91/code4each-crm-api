<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\AgencyWebsite;
use App\Models\User;
use App\Models\WebsiteCategory;
use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Undefined;
use Illuminate\Support\Facades\Validator;

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

    public function agencyDetails(Request $request)
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'category_id' => 'required',
            'description' => 'nullable|string',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $validate = $validator->valid();

        $updateAgencyDetail = Agency::where("id", $validate['agency_id'])->update([
            'category_id' => $validate['category_id'],
            'address' => $validate['address'],
            'description'  => $validate['description'] ?? null
        ]);
        if ($updateAgencyDetail) {
            $response = [
                'message' => "Agency Detail Saved Successfully.",
                'success' => true,
                'status' => 200,
            ];
        }

      return response()->json($response);
    }

    public function getWebsiteCategories()
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];

        $categories = WebsiteCategory::all();
        $response = [
            'message' => "Categories fetched Successfully.",
            'success' => true,
            'categories' => $categories,
            'status' => 200,
        ];

        return response()->json($response);

    }
    public function agencyWebsiteDetails(Request $request)
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'category_id' => 'required',
            'description' => 'nullable|string',
            'address' => 'required',
            'business_name' => 'required',
            'logo' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validate = $validator->valid();

        $websitesData = Websites::first();
        $agencyWebsiteDetails = AgencyWebsite::updateOrCreate([
            'website_category_id' => $validate['category_id'],
            'address' => $validate['address'],
            'description'  => $validate['description'] ?? null ,
            'agency_id' => $validate['agency_id'],
            'business_name' => $validate['business_name'],
            'logo' => $validate['logo'] ?? null,
            'website_id' => $websitesData->id,
            'created_by' => auth()->user()->id,
        ]);
        $agency_website_id = $agencyWebsiteDetails->id;
        if ($agencyWebsiteDetails) {
            $websitesObj =  Websites::first();
            $websitesObj->assigned = $agency_website_id;
            $websitesObj->save();
            $response = [
                'message' => "Agency Website Detail Saved Successfully.",
                'success' => true,
                'status' => 200,
            ];
        }

      return response()->json($response);

    }

    // public function getAgencyWebsiteInfo($agency_id)
    // {
    //     $response = [
    //         'success' => false,
    //         'status' => 400,
    //     ];
    //     // dd($request);


    //     $agencyWebsiteInfo = AgencyWebsite::where('agency_id', $agency_id)->get();
    //     // $agencyWebsiteInfo= AgencyWebsite::join('websites', 'agency_website.website_id', '=', 'websites.id')->where('agency_id','=', $agency_id)->get();

    //     dd($agencyWebsiteInfo);

    // }
}
