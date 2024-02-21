<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use GrahamCampbell\ResultType\Success;

class WordpressMenusController extends Controller
{
    /**
     * THIS METHOS IS FOR CREATING WORDPRESS MENUS
     */

    public function getWordpressMenus(Request $request) {
        $websiteUrl = $request->input('website_url');
        $getApiUrl = $websiteUrl . '/wp-json/v1/get-menus';
        $getMenusResponse = Http::get($getApiUrl);
        
        if ($getMenusResponse->successful()) {
            $response['response'] =$getMenusResponse->json()['data'];
            $response['status'] = $getMenusResponse->status();
            $response['success'] = true;
        }
            else{
                $response['response'] = $getMenusResponse->json();
                $response['status'] = 400;
                $response['success'] = false;
            }
        return $response;
    
    }

    /**
     * POST MENUS
     */

     public function postWordpressMenus(Request $request) {
        $response = [
            'success' => false,
            'status' => 400,
        ];

        $validator = Validator::make($request->all(), [
            'website_url'=>'required',
            'menu_data.name' => 'required',
            'menu_data.value' => 'required', 
            'menu_data.menu_type' => 'required', 
            'menu_data.menu_value_type' => 'required',
            'menu_data.position' => 'required|numeric',

        ]);

        if ($validator->fails()) {
           return response()->json(['response' => $validator->errors(),'status' => 400, 'success'=> false], 400);
        }

        $validate = $validator->valid();
        
        $postData = $validate['menu_data'];
       
        //INSERT DATA HERE
        $websiteUrl = $request->input('website_url');
        $postApiUrl = $websiteUrl . '/wp-json/v1/add-menu';
        $postMenuResponse = Http::post($postApiUrl, $postData);
        if($postMenuResponse->successful()){
            //dump($postMenuResponse->json()); dd('---');
            $response['response'] =$postMenuResponse->json()['success'];
            $response['status'] = $postMenuResponse->status();
            $response['success'] = true;
        } else{
            $response['response'] = $postMenuResponse->json();
            $response['status'] = 400;
            $response['success'] = false;
        }
        return $response;

    }

    //

}  