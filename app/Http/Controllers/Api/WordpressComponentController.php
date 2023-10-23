<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FontFamily;
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

    public function setDefaultColorOrFont($websiteUrl,$typeValue) {

        if(!$websiteUrl){
            return response()->json(["error" => "website Url is required to Proceed."]);
        }
        if(!$typeValue){
            return response()->json(["error" => "Value is required to Update."]);
        }
          $setDefaultColorOrFontUrl = $websiteUrl . '/wp-json/v1/update-options-value';
          $setDefaultColorOrFontResponse = Http::post($setDefaultColorOrFontUrl,$typeValue);
              if ($setDefaultColorOrFontResponse->successful()) {
                  $response['response'] =$setDefaultColorOrFontResponse->json();
                  $response['status'] = $setDefaultColorOrFontResponse->status();
                  $response['success'] = true;
              }
              else{
                  $response['response'] = $setDefaultColorOrFontResponse->json();
                  $response['status'] = 400;
                  $response['success'] = false;
              }
          return $response;
      }

      public function addWordpressFontFamily($websiteUrl, $id= false)
      {
        if(!$websiteUrl){
            return response()->json(["error" => "website Url is required to Proceed."]);
        }
        if ($id) {
            $randomFontFamily = FontFamily::where('id',$id)->first();
        } else {
            $randomFontFamily = FontFamily::inRandomOrder()->first();
        }

        $fontId['c4e_font_family_id'] =  $randomFontFamily->id;
        $fontFamilyName =  $randomFontFamily->name;
        $fontFamilyData = array('c4e_font_family' =>["value" => $fontFamilyName]);
        $addFontFamilyUrl = $websiteUrl . 'wp-json/v1/change_global_variables';
        $addFontFamilyResponse = Http::post($addFontFamilyUrl,$fontFamilyData);
        if($addFontFamilyResponse->successful()){
                 $response['response'] =$addFontFamilyResponse->json();
                 $response['response']['font_id'] = $fontId;
                 $response['status'] = $addFontFamilyResponse->status();
                 $response['success'] = true;
          } else{
            $response['response'] = $addFontFamilyResponse->json();
            $response['status'] = 400;
            $response['success'] = false;
        }
        return $response;
     }
}
