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
        $type = request()->input('type');
        if($type){
            $componentData = Component::where('type',$type)->where('status','active')->get();
            $componentDetail = [];
            foreach($componentData as $data){
               $component = [];
               $component['id'] = $data->id;
               $component['component_unique_id'] = $data->component_unique_id;
               $component['preview'] = '/storage/'.$data->preview;
               $component['type'] = $data->type;
               $component['category'] = $data->category;
               $componentDetail[] = $component;
            }
        }else{
             $componentData = Component::where('status','active')->get();
             $componentDetail = [];
             foreach($componentData as $data){
                $component = [];
                $component['id'] = $data->id;
                $component['component_unique_id'] = $data->component_unique_id;
                $component['preview'] = '/storage/'.$data->preview;
                $component['type'] = $data->type;
                $component['category'] = $data->category;
                $componentDetail[] = $component;
             }
        }
        if($componentDetail){
            $response = [
                "message" => "Result Fetched Successfully.",
                'component' => $componentDetail,
                "success" => true,
                "status"  => 200,
            ];
        }

        return $response;
    }
}
