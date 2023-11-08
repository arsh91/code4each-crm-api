<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\ComponentFormFields;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\WordpressComponentController;
use App\Http\Requests\getFormFields;
use App\Http\Requests\UpdateFormFields;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Validator;

class CustomComponentFieldsController extends Controller
{
    private $wordpressComponentClass;
    public function __construct()
    {
       $this->wordpressComponentClass = new WordpressComponentController();
    }

    public function getFormFields(getFormFields $request)
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];

        $validated = $request->validated();

        $componentUniqueId =  $validated['component_unique_id'];
        $website_url =  $validated['website_url'];

        $component = Component::with('formFields')->where('component_unique_id', $componentUniqueId)->select('id', 'type', 'category')->first();

        $componentFormFields = $component->formFields;

        foreach ($componentFormFields as $formField) {

            $formFieldArray = [
                "field_name" =>  $formField->field_name,
                "field_type" => $formField->field_type,
                "default_value" => $formField->default_value,
                "meta1" => $formField->meta_key1,
                "meta2" => $formField->meta_key2,
                "value"=> null
            ];

            $formFieldsArray[] = $formFieldArray;
        }

        $fieldNames = array_filter(array_map(function($item) {
            return isset($item['field_name']) ? $item['field_name'] : null;
        }, $formFieldsArray));


        $getComponentFieldsResponse = $this->wordpressComponentClass->getInsertedComponentFields(
                $website_url,
                $fieldNames
            );

            if ($getComponentFieldsResponse['success'] && $getComponentFieldsResponse['response']['status'] == 200) {

                $replacementArray = $getComponentFieldsResponse['response']['data'];

                $formFieldsArray = array_map(function($formField) use ($replacementArray) {
                    $field_name = $formField['field_name'];
                    if (isset($replacementArray[$field_name])) {
                        $formField['value'] = $replacementArray[$field_name];
                    }
                    return $formField;
                }, $formFieldsArray);
            }


        $response = [
            "message" => "Detail Fetched Successfully.",
            'data' => $formFieldsArray,
            'success' => true,
            'status' => 200,

        ];

        return response()->json($response);
    }

    public function updateComponentFormFields(UpdateFormFields $request)
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];

        $validated = $request->validated();
        $componentUniqueId = $validated['component_unique_id'];
        $website_url =  $validated['website_url'];

            $formFields = $validated['form_fields'];
            $component = Component::with('formFields')->where('status','active')->where('component_unique_id', $componentUniqueId)->select('id', 'type', 'category')->first();
            $componentFormFields = $component->formFields;

            $formFieldNames = array_column($formFields, 'field_name');
            $componentFormFieldNames = $componentFormFields->pluck('field_name')->toArray();

            $keysMatched = empty(array_diff($formFieldNames, $componentFormFieldNames));

            if ($keysMatched) {
                $updateFieldsResponse = $this->wordpressComponentClass->insertOrUpdateComponentFields($formFields, $website_url);
            } else {
                $error_message = "Error while updating component form fields details";
                    $response = [
                        "success" => false,
                        "message" => $error_message,
                        "status" => 400,
                    ];
            }

            if ($updateFieldsResponse['success'] && $updateFieldsResponse['response']['status'] == 200) {
                $response = [
                            "message" => "Record Updated Successfully.",
                            "success" => true,
                            "status" => 200,
                        ];
            }

        return response()->json($response);
    }
}
