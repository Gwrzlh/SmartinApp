<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\mentors;
use App\Models\subjects;

class mentorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filterbysubject = $request->get('filterbysubject');

        $subjects = subjects::all();
        $mentors = mentors::with('subjects')->when($search, function ($query) use ($search) {
            return $query->where('mentor_name', 'like', "%{$search}%")
                            ->orWhere('gender', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
        })->when($filterbysubject, function ($query) use ($filterbysubject) {
            return $query->where('specialization_id', $filterbysubject);
        })->paginate(5)->withQueryString();

        return view('Admin.mentor.index', compact('mentors', 'subjects'));
    }
    public function create()
    {
        $subjects = subjects::all();
        return view('Admin.mentor.create', compact('subjects'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'mentor_name' => 'required',
            'gender' => 'required',
            'phone_number' => 'required',
            'spesialization_id' => 'required',
            'isActive' => 'required|boolean',
        ]);
        mentors::create([
            'mentor_name' => $request->mentor_name,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'specialization_id' => $request->spesialization_id,
            'is_active' => $request->isActive,
        ]);

        return redirect()->route('admin.mentor.index')->with('success', 'Mentor created successfully.');
    }
    public function edit(mentors $mentor)
    {
        $subjects = subjects::all();
        return view('Admin.mentor.update', compact('mentor', 'subjects'));
    }
    public function update(Request $request, mentors $mentor)
    {
        $request->validate([
            'mentor_name' => 'required',
            'gender' => 'required',
            'phone_number' => 'required',
            'specialization_id' => 'required',
            'is_active' => 'required|boolean',
        ]);

        mentors::where('id', $mentor->id)->update([
            'mentor_name' => $request->mentor_name,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'specialization_id' => $request->specialization_id,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('mentor.index')->with('success', 'Mentor updated successfully.');
    }
    public function destroy(mentors $mentor)
    {
        $mentor->delete();
        return redirect()->route('mentor.index')->with('success', 'Mentor deleted successfully.');
    }
    public function show($id)
    {
         $mentor = mentors::with('subjects')->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'mentor_name'   => $mentor->mentor_name,
                'gender'        => $mentor->gender,
                'phone_number'  => $mentor->phone_number,
                'specialization'=> $mentor->subjects->subject_name ?? 'Tidak ada spesialisasi',
                'is_active'     => $mentor->is_active,
                'created_at'    => $mentor->created_at->toISOString(),
                'updated_at'    => $mentor->updated_at->toISOString(),
            ]);
        }

            return abort(404);
    }
}
