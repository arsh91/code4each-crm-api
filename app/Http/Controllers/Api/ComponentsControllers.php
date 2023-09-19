<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComponentsControllers extends Controller
{
    public function getComponents()
    {
        $response = [
            'success' => false,
            'status' => 400,
        ];
        return response()->json($response);
    }
}
