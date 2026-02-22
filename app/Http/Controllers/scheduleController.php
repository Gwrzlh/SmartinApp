<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\schedules;

class scheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = schedules::all();
        return view('Admin.schedule.index', compact('schedules'));
    }
}
