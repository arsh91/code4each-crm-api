<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgencyWebsite;
use App\Models\Component;
use App\Models\ComponentDependency;
use App\Models\User;
use App\Models\Websites;
use App\Notifications\CommonEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ComponentsControllers extends Controller
{
    public function getComponent()
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];
        $component = Component::first();
        $componentDetail['component'] = $component;
        $componentDependencies = ComponentDependency::where('component_id',$component->id)->get();
        if(  $componentDependencies){
            $componentDetail['component_dependencies'] = $componentDependencies;
        }
        $response = [
            'success' => true,
            'status' => 200,
            'data' => $componentDetail,
        ];
        return response()->json($response);
    }

    public function agencyWebsiteDetails(Request $request)
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'category_id' => 'required',
            'description' => 'nullable|string',
            'address' => 'required',
            'business_name' => 'required',
            'logo' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validate = $validator->valid();
        try {
            DB::beginTransaction();
            $websitesData = Websites::where('assigned', null)->first();
            $agencyWebsiteDetails = AgencyWebsite::create([
                'website_category_id' => $validate['category_id'],
                'address' => $validate['address'],
                'description'  => $validate['description'] ?? null,
                'agency_id' => $validate['agency_id'],
                'business_name' => $validate['business_name'],
                'created_by' => auth()->user()->id,
            ]);
            if ($request->hasFile('logo')) {
                $uploadedFile = $request->file('logo');
                $filename = time() . '_' . $uploadedFile->getClientOriginalName();
                $uploadedFile->storeAs('public/AgencyWebsiteDetails', $filename);
                $path = 'AgencyWebsiteDetails/' . $filename;
                $agencyWebsiteDetails->logo = $path;
                $agencyWebsiteDetails->save();

            }
            DB::commit();

            if($agencyWebsiteDetails->agency_id && $websitesData->website_domain){
                $result = $this->sendComponentToWordpress($agencyWebsiteDetails->agency_id, $websitesData->website_domain);
                if ($result['success'] == true && $result['response']['status'] == 200) {
                    $websiteUrl = $result['domain'];

                    //assigned site to user
                    AgencyWebsite::where('id', $agencyWebsiteDetails->id)->update(['website_id' => $websitesData->id]);
                    Websites::where('id', $websitesData->id)->update(['assigned' => $agencyWebsiteDetails->id]);

                    // send mail to user
                    $recipient = User::find($agencyWebsiteDetails->created_by);
                    $messages = [
                        'greeting-text' => "Hey User,",
                        'subject' => 'Your Domain is Ready',
                        'additional-info' => 'Need assistance? Contact us at [support@code4eachcrm.com] or [SupportPhone: +1 (555) 123-4567].',
                        'lines_array' => [
                            'title' => 'Your domain is now ready for use after successfully updating your agency details. Enjoy a seamless online presence with the latest information.',
                            'body-text' => 'Here Is The Details For Your Website',
                            'special_Agency_Name' => $agencyWebsiteDetails->business_name ,
                            'special_Domain_Name' => $websiteUrl,
                        ],
                    ];
                    $recipient->notify(new CommonEmailNotification($messages));

                    $response = [
                        'message' => "Agency Website Detail Saved Successfully.",
                        'success' => true,
                        'status' => 200,
                    ];
                } else {
                    DB::rollBack();
                    $response = [
                        'message' => "An error occurred.",
                        'success' => false,
                        'status' => 400,
                    ];
                }
            }


        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
            // Logging the error in log file
            \Log::error("\nError: $errorMessage\nFile: $errorFile\nLine: $errorLine \nCode:$errorCode");

            $response = [
                'message' => "An error occurred.",
                'success' => false,
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        } finally {
            DB::commit();
        }
        return response()->json($response);
    }

    private function uploadLogoToWordpress($agencyId,$url)
    {
        $agencyWebsiteDetail = AgencyWebsite::where('agency_id',$agencyId)->where('status','active')->first();
        $imagePath = $agencyWebsiteDetail->logo;
        $thirdPartyUrl = $url . 'wp-json/v1/logo/';
        $imageFullPath = storage_path('app/public/' . $imagePath);
        if (file_exists($imageFullPath)) {
            $logoResponse = Http::attach(
                'logo',
                file_get_contents($imageFullPath),
                'logo.png'
            )
            ->post($thirdPartyUrl);
            \Log::info("Wordpress Logo Response: " . $logoResponse->body());
                $response = [
                     $logoResponse->json(),
                    'status' =>  $logoResponse->status(),
                ];
        } else {
            $response = [
                'message' => "Error Occurs In Uploading the Logo",

            ];
        }
        return $response;
    }

    public function sendComponentToWordpress($agency_id, $websiteUrl, $regenerateFlag = false)
    {
        $response = [
            'success' => false,
        ];
        if ($regenerateFlag) {
            $components = $this->generateComponents($agency_id, $websiteUrl);
        } else {
            $uploadLogo =  $this->uploadLogoToWordpress($agency_id, $websiteUrl);
            $components = $this->generateComponents($agency_id, $websiteUrl);
        }
        $startAddComponentUrl = $websiteUrl . 'wp-json/v1/installation';
        $startAddResponse = Http::post($startAddComponentUrl, ['start' => true]);
        if ($startAddResponse->successful()) {
            $position = 1;
            foreach ($components as $component) {
                $componentData = [
                    'component_detail' => [
                        'component_name' => $component->component_name,
                        'path' => $component->path,
                        'type' => $component->type,
                        'position' => null,
                        'status' => $component->status,
                    ],
                    'component_dependencies' => ComponentDependency::where('component_id', $component->id)
                        ->select('component_id', 'name', 'type', 'path', 'version')
                        ->get(),
                ];
                if ($component->type === 'header') {
                    $componentData['component_detail']['position'] = 1;
                } elseif ($component->type === 'footer') {
                    $componentData['component_detail']['position'] = count($components);
                } else {
                    $componentData['component_detail']['position'] = $position + 1;
                    $position++;
                }
                $postUrl = $websiteUrl . 'wp-json/v1/component';
                $componentResponse = Http::post($postUrl, $componentData);
                if ($componentResponse->successful()) {
                    $data['response'] = $componentResponse->json();
                    $data['status'] = $componentResponse->status();
                    $data['domain'] = $websiteUrl;
                } else {
                    $errorCode = $componentResponse->status();
                    $errorData = $componentResponse->json();
                    $data['error'] = $errorData . ' ' . $errorCode;
                    $data['success'] = false;
                }
            }
        } else {
            $data['response'] = $startAddResponse->json();
            $data['success'] = false;
            $data['status'] = 400;
        }
        $endAddComponentResponse = Http::post($startAddComponentUrl, ['start' => false]);
        if ($endAddComponentResponse->successful()) {
            $data['response'] = $endAddComponentResponse->json();
            $data['success'] = true;
        }
        $response = $data;

        return $response;
    }


    private function generateComponents($agency_id, $websiteUrl)
    {
        $agencyWebsiteDetail = AgencyWebsite::with('websiteCategory')->where('agency_id', $agency_id)->where('status', 'active')->first();
        $websiteCategory = $agencyWebsiteDetail->websiteCategory->name;
        $types = ['header', 'footer', 'about_section', 'service_section', 'section'];
        $components = [];

        foreach ($types as $type) {
            $randomComponent = $this->getRandomComponent($type, $websiteCategory);
            if ($randomComponent) {
                $components[] = $randomComponent;
            }
        }
        return $components;
    }

    private function getRandomComponent($type, $category)
    {
        return Component::where('type', $type)
            ->where('category', $category)->where('status','active')
            ->inRandomOrder()->first();
    }

    public function regenerateComponents(Request $request)
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];

        $validator = Validator::make($request->all(), [
            'agency_id' => 'required',
            'website_url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if (isset($request->agency_id, $request->website_url)) {
            $agency_id = $request->agency_id;
            $website_url = $request->website_url;
            $regenerate = true;
            $regenerateComponentResponse = $this->sendComponentToWordpress($agency_id,$website_url,$regenerate);
            if($regenerateComponentResponse['response']['status'] == 200 && $regenerateComponentResponse['success'] == true){
                $response = [
                    'message' => "Components Re-generated Successfully.",
                    'success' => true,
                    'status' => 200,
                ];
            }

        } else {
            $response = [
                'message' => "Need to Pass Required Proper Arguments.",
                'success' => false,
                'status' => 400,
            ];
        }
        return $response;
    }
}
