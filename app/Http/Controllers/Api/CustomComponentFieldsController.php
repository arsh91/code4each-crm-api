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
            $getComponentFieldsResponse = $this->wordpressComponentClass->getInsertedComponentFields(
                $website_url,
                $formField->field_name
            );
            $value = null;
            if ($getComponentFieldsResponse['success'] && $getComponentFieldsResponse['response']['status'] == 200) {
                $value = $getComponentFieldsResponse['response']['data'][$formField->field_name];
            }

            $formFieldArray = [
                "field_name" =>  $formField->field_name,
                "field_type" => $formField->field_type,
                "default_value" => $formField->default_value,
            ];

            // Add the 'value' key only if $value exists
            if ($value !== null) {
                $formFieldArray['value'] = $value;
            }
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

    public function updateComponentFormFields(UpdateFormFields $request)
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];

        $validated = $request->validated();
        $componentUniqueId = $validated['component_unique_id'];
        $website_url =  $validated['website_url'];
        $field_name =  $validated['field_name'];
        $default_value =  $validated['default_value'];

        $component = Component::with('formFields')->where('component_unique_id', $componentUniqueId)->select('id', 'type', 'category')->first();

        $componentFormFields = $component->formFields;

        foreach ($componentFormFields as $formField) {
            $formFieldArray = [
                "field_name" =>  $field_name,
                "field_type" => $formField->field_type,
                "default_value" => $default_value,
                'type' => $component->type,
            ];
            $updateLogoToWordPressResponse[] = $this->wordpressComponentClass->insertOrUpdateComponentFields($formFieldArray, $website_url);
        }
        $allResponsesSuccessful = true;

        foreach ($updateLogoToWordPressResponse as $responseItem) {
            $status = $responseItem['response']['status'];
            if ($status !== 200) {
                $allResponsesSuccessful = false;
                break;
            }
        }

        if ($allResponsesSuccessful) {
            $message = "All component details updated successfully";
            $response = [
                "success" => true,
                "message" => $message,
                "status" => 200,
            ];
        } else {
            $error_message = "Error while updating component details";
            $response = [
                "success" => false,
                "message" => $error_message,
                "status" => 400,
            ];
        }

        return response()->json($response);
    }
}
