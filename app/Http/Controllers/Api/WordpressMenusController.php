<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WordpressMenusController extends Controller
{
    /**
     * THIS METHOS IS FOR CREATING WORDPRESS MENUS
     */

    public function getWordpressMenus(Request $request) {
    $websiteUrl = $request->input('website_url');
    $websiteUrl = $websiteUrl . '/wp-json/v1/get-menus';
    $getMenusResponse = Http::get($websiteUrl);
    
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
            'name' => 'required',
            'value' => 'required', 
            'menu_type' => 'required', 
            'menu_value_type' => 'required',
            'position' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validate = $validator->valid();
        dump($validate);

    }

}