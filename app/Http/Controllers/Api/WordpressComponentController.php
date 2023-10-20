<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WordpressComponentController extends Controller
{
    public static function deleteComponent($websiteUrl = false, $componentUniqueId = false)
    {
        if(!$websiteUrl){
            return response()->json(["error" => "website Url is required to Proceed."]);
        }
        if(!$componentUniqueId){
            return response()->json(["error" => "Component Unique Id is required to Proceed."]);
        }
        $deleteComponentUrl = $websiteUrl . '/wp-json/v1/component';
        $deleteComponentResponse = Http::delete($deleteComponentUrl,$componentUniqueId);
            if ($deleteComponentResponse->successful()) {
                // $responseData = $getActiveComponentResponse->json();
                $response['response'] =$deleteComponentResponse->json();
                $response['status'] = $deleteComponentResponse->status();
                $response['success'] = true;
            }
            else{
                $response['response'] = $deleteComponentResponse->json();
                $response['status'] = 400;
                $response['success'] = false;
            }
        return $response;
    }
    public function getDefaultColorOrFont($websiteUrl,$typeKey) {

      $bodyJson = json_encode([
                $typeKey
            ]) ;
        $getDefaultColorOrFontUrl = $websiteUrl . '/wp-json/v1/get-options-value';
        $colorOrFontValueResponse = Http::withBody($bodyJson, 'application/json')->get($getDefaultColorOrFontUrl);
            if ($colorOrFontValueResponse->successful()) {
                $response['response'] =$colorOrFontValueResponse->json();
                $response['status'] = $colorOrFontValueResponse->status();
                $response['success'] = true;
            }
            else{
                $response['response'] = $colorOrFontValueResponse->json();
                $response['status'] = 400;
                $response['success'] = false;
            }
        return $response;
    }
}
