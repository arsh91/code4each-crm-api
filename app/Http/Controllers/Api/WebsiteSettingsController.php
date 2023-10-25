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
        $websiteData = Websites::with('agencyWebsiteDetail')->where('id', $website_id)->first();
        $agencyWebsiteDetail = $websiteData->agencyWebsiteDetail;
        $logo = null;
        if($logo){
            $logo = '/storage/'.$agencyWebsiteDetail->logo;
        }
        $websiteDetail = [
            "id" => $websiteData->id,
            "website_domain" => $websiteData->website_domain,
            "assigned" => $websiteData->assigned,
            "agency_website_detail" =>[
                "id" => $agencyWebsiteDetail->id,
                "business_name" => $agencyWebsiteDetail->business_name,
                "website_category_id" => $agencyWebsiteDetail->website_category_id,
                "address" => $agencyWebsiteDetail->address,
                "description" => $agencyWebsiteDetail->description,
                "logo" => $logo,
                "agency_id" => $agencyWebsiteDetail->agency_id,
                "website_id" => $agencyWebsiteDetail->website_id,
                "status" => $agencyWebsiteDetail->status,
                "created_by" => $agencyWebsiteDetail->created_by,
            ],
        ];
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
