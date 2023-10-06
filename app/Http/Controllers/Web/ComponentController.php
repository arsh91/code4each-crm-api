<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\ComponentDependency;
use App\Models\ComponentFormFields;
use App\Models\WebsiteCategory;
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
            'preview.*' => 'file|mimes:jpg,jpeg,png,gif|max:5000',
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
            // return response()->json(['errors' => $validator->errors()], 400);
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
                ]);
            }
                foreach ($validate['form-fields'] as $formFieldData) {
                    ComponentFormFields::create([
                        'component_id' => $component->id,
                        'field_name' => $formFieldData['name'],
                        'field_type' => $formFieldData['type'],
                        'default_value' => $formFieldData['default_value'],
                    ]);
                }
            // $request->session()->flash('message','Component Saved Successfully.');
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
        //
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
        //
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
