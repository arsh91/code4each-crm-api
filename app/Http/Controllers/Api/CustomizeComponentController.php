<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\ComponentController;
use App\Models\Component;
use Illuminate\Http\Request;

class CustomizeComponentController extends Controller
{
    public function fetchComponent()
    {
        $response = [
            "success" => false,
            "status"  => 400,
        ];
        $componentData = Component::select('id','component_unique_id','preview','type','category')->where('status','active')->get();
        // dd($componentData->toArray());
        if($componentData){
            $response = [
                "message" => "Result Fetched Successfully.",
                'component' => $componentData,
                "success" => true,
                "status"  => 200,
            ];
        }

        return $response;
    }
}
