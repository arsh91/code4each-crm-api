<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgencyWebsite;
use App\Models\Component;
use App\Models\ComponentDependency;
use App\Models\Websites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

    public function index()
    {
        $response = Http::get('http://jsonplaceholder.typicode.com/posts');

        $jsonData = $response->json();

        dd($jsonData);
    }

    public function store()
    {
        $response = Http::post('http://jsonplaceholder.typicode.com/posts', [
                    'title' => 'This is test from tutsmake.com',
                    'body' => 'This is test from tutsmake.com as body',
                ]);
            dd($response);
        dd($response->successful());
    }

    public function sendComponentToWordpress(){
        $user = auth()->user();
        $agencyId = $user->agency_id;
        $agencyWebsiteDetail = AgencyWebsite::with('websiteCategory')->where('agency_id',$agencyId)->where('status','active')->first();
        $websiteCategory = $agencyWebsiteDetail->websiteCategory->name;
        $website = Websites::where('assigned', $user->id)->first();
        $websiteUrl = $website->website_domain;
        $components = Component::select('id', 'component_name', 'path', 'type', 'status')
        ->whereIn('type', ['header', 'footer'])
        ->orWhere(function ($query) {
            $query->where('type', 'section')
                ->take(3);
        })
        ->where('category', $websiteCategory)
        ->get();

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
            $response = Http::post($postUrl, $componentData);

            if ($response->successful()) {
                $data = $response->json();
            } else {
                $errorCode = $response->status();
                $errorData = $response->json();
                dd($errorData);
            }
        }
        dd($response->json());
    }

}
