<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\ComponentDependency;
use App\Models\ComponentFormFields;
use App\Models\WebsiteCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $components = Component::all();
        return view('components.index',compact('components'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = WebsiteCategory::all();
        return view('components.create',compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'component_name' => 'required|string|max:255',
            'path' => 'required',
            'type' => 'required',
            'category' => 'required',
            'preview' => 'required|file|mimes:jpg,jpeg,png,gif|max:5000',
            'dependencies' => 'required|array',
            'dependencies.*.name' => 'required',
            'dependencies.*.type' => 'required',
            'dependencies.*.path' => 'required',
            'dependencies.*.version' => 'required',
            'form-fields' => 'required|array',
            'form-fields.*.name' => 'required',
            'form-fields.*.type' => 'required',
            'form-fields.*.default_value' => 'required',

        ]);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }
        $validate = $validator->valid();
        $category = implode(",",$validate['category'] );
        $component = Component::create([
            'component_name' => $validate['component_name'],
            'path' => $validate['path'],
            'type'  => $validate['type'],
            'category' => $category,
        ]);
        if ($request->hasFile('preview')) {
            $uploadedFile = $request->file('preview');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $uploadedFile->storeAs('public/Components', $filename);
            $path = 'Components/' . $filename;
            $component->preview = $path;
            $component->save();
        }
        if ($component) {
            $componentName = str_replace(' ', '_', $component->component_name);
            $uniqueId = strtoupper('comp_' . $componentName . '_' . $component->id);
            Component::where('id', $component->id)->update(['component_unique_id' => $uniqueId]);
            foreach ($validate['dependencies'] as $dependencyData) {
                ComponentDependency::create([
                    'component_id' => $component->id,
                    'name' => $dependencyData['name'],
                    'type' => $dependencyData['type'],
                    'path' => $dependencyData['path'],
                    'version' => $dependencyData['version'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
                foreach ($validate['form-fields'] as $formFieldData) {
                    ComponentFormFields::create([
                        'component_id' => $component->id,
                        'field_name' => $formFieldData['name'],
                        'field_type' => $formFieldData['type'],
                        'default_value' => $formFieldData['default_value'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            $message = "Component Saved Successfully.";
            return redirect()->route('components.index')->with('message', $message);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = WebsiteCategory::all();
        $componentData = Component::with('dependencies','formFields')->find($id);
        return view('components.edit',compact('category','componentData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'edit_component_name' => 'required|string|max:255',
            'edit_path' => 'required',
            'edit_type' => 'required',
            'edit_category' => 'required',
            'edit_preview.*' => 'file|mimes:jpg,jpeg,png,gif|max:5000',
            'edit_dependencies' => 'required|array',
            'edit_dependencies.*.name' => 'required',
            'edit_dependencies.*.type' => 'required',
            'edit_dependencies.*.path' => 'required',
            'edit_dependencies.*.version' => 'required',
            'edit_form-fields' => 'required|array',
            'edit_form-fields.*.name' => 'required',
            'edit_form-fields.*.type' => 'required',
            'edit_form-fields.*.default_value' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }
        $validate = $validator->valid();
        $dependencyData = $validate['edit_dependencies'];
        $formFieldsData  = $validate['edit_form-fields'];
        $componentDetail = Component::with('dependencies','formFields')->find($id);
        $formFieldsDetail = $componentDetail->formFields->toArray();
        $dependencyDetail = $componentDetail->dependencies->toArray();
        $category = implode(",",$validate['edit_category'] );
        $component = Component::where('id',$id)->update([
            'component_name' => $validate['edit_component_name'],
            'path' => $validate['edit_path'],
            'type'  => $validate['edit_type'],
            'category' => $category,
        ]);

        //Only if Needs to update the preview image then this will update the image
        if ($request->hasFile('edit_preview')) {
            $uploadedFile = $request->file('edit_preview');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $uploadedFile->storeAs('public/Components', $filename);
            $path = 'Components/' . $filename;
            Component::where('id',$id)->update(['preview' => $path]);
        }
        if ($component) {
            if($validate['edit_component_name'] != $componentDetail->component_name){
                $componentName = str_replace(' ', '_', $component->component_name);
                $uniqueId = strtoupper('comp_' . $componentName . '_' . $component->id);
                Component::where('id', $component->id)->update(['component_unique_id' => $uniqueId]);
            }

            //Create or Update the Dependencies
            foreach ($dependencyData as $dependency) {
                if (isset($dependency['id'])) {
                     ComponentDependency::where('id', $dependency['id'])
                        ->where('component_id', $id)
                        ->update([
                            'name' => $dependency['name'],
                            'type' => $dependency['type'],
                            'path' => $dependency['path'],
                            'version' => $dependency['version'],
                            'updated_at' => now(),
                        ]);
                } else {
                      ComponentDependency::create([
                        'component_id' => $id,
                        'name' => $dependency['name'],
                        'type' => $dependency['type'],
                        'path' => $dependency['path'],
                        'version' => $dependency['version'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            //Delete Removed Dependencies From dependencyArray
            $unmatchedDependencies = array_filter($dependencyDetail, function($record) use ($dependencyData) {
                return !in_array($record['id'], array_column($dependencyData, 'id'));
            });
            if($unmatchedDependencies){
                foreach ($unmatchedDependencies as  $umDependency) {
                    ComponentDependency::where('id',$umDependency['id'])->delete();
                }
            }

            //Create or Update the formFields
            foreach ($formFieldsData as $formFieldData) {
                if (isset($formFieldData['id'])) {
                    ComponentFormFields::where('id', $formFieldData['id'])
                        ->where('component_id', $id)
                        ->update([
                            'field_name' => $formFieldData['name'],
                            'field_type' => $formFieldData['type'],
                            'default_value' => $formFieldData['default_value'],
                            'updated_at' => now(),
                        ]);
                } else {
                    ComponentFormFields::create([
                        'component_id' => $id,
                        'field_name' => $formFieldData['name'],
                        'field_type' => $formFieldData['type'],
                        'default_value' => $formFieldData['default_value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

             //Delete Removed FormFields From fromFieldsArray
             $unmatchedFormFields = array_filter($formFieldsDetail, function($record) use ($formFieldsData) {
                return !in_array($record['id'], array_column($formFieldsData, 'id'));
            });
            if($unmatchedFormFields){
                foreach ($unmatchedFormFields as  $umFields) {
                    ComponentFormFields::where('id',$umFields['id'])->delete();
                }
            }

            $message = "Component Updated Successfully.";
            return redirect()->back()->with('message', $message);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
