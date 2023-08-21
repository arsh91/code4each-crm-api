<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mockery\Undefined;

class DashboardController extends Controller
{
    public function index() 
    {
        dd("dashboard");
    }
}
