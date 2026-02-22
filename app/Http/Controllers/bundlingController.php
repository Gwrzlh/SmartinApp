<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\bundlings;

class bundlingController extends Controller
{
    public function index(Request $request)
    {
        $bundlings = bundlings::all();
        return view('Admin.bundling.index', compact('bundlings'));
    }
}
