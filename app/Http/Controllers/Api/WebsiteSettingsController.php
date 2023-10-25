<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgencyWebsite;
use App\Models\Websites;
use Illuminate\Http\Request;

class WebsiteSettingsController extends Controller
{
    public function settings(Request $request)
    {
        $website_id = $request->input('website_id');
        if(!$website_id){
            return response()->json(["error" => "website id is required."],400);
        }
        $websiteDetail = Websites::with('agencyWebsiteDetail')->where('id', $website_id)->select(['id', 'website_domain', 'assigned'])->first()->toArray();

        if (!empty($websiteDetail)) {
            $response = [
                'message' => "Detail Fetched Successfully.",
                'success' => true,
                'status' => 200,
                'settings_detail' => $websiteDetail
            ];
        } else {
            $response = [
                'message' => "No details found.",
                'success' => false,
                'status' => 404,
                'settings_detail' => []
            ];
        }

        return response()->json($response);
    }
}
