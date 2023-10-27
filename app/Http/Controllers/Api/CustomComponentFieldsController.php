<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\ComponentFormFields;
use Illuminate\Http\Request;

class CustomComponentFieldsController extends Controller
{
    public function getFormFields(Request $request)
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];
       $componentUniqueId =  $request->component_unique_id;
       $component = Component::with('formFields')->where('component_unique_id',$componentUniqueId)->select('id','type','category')->first();
       $componentFormFields = $component->formFields;
       foreach ($componentFormFields as $formField) {
        $formFieldArray = [
            "id" =>  $formField->id,
            "field_name" =>  $formField->field_name,
            "field_type" => $formField->field_type,
            "default_value" => $formField->default_value,
        ];
        $formFieldsArray[] = $formFieldArray;
       }

       $response = [
            "message" => "Detail Fetched Successfully.",
            'data' => $formFieldsArray,
            'success' => true,
            'status' => 200,

        ];

        return response()->json($response);

    }
}
