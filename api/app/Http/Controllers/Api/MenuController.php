<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    function get_all()  {
         return response()->json([
            'status' => false,
            'message' => "just a test for verify authentification"
        ], 200);
    }
}
