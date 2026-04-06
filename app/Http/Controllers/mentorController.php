<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\mentors;
use App\Models\subjects;
use Exception;
use Illuminate\Support\Facades\DB;

class mentorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filterbysubject = $request->get('filterbysubject');

        $subjects = subjects::all();

        $mentors = mentors::with('subjects')
            ->when($search, function ($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('mentor_name', 'like', "%{$search}%")
                    ->orWhere('gender', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->when($filterbysubject, function ($query) use ($filterbysubject) {
                return $query->whereHas('subjects', function ($q) use ($filterbysubject) {
                    $q->where('subjects.id', $filterbysubject);
                });
            })
            ->latest()  
            ->paginate(5)
            ->withQueryString();

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
            'phone_number' => 'required'
        ]);

        $isActive = $request->has('isActive') ? 1 : 0;

        try{
          db::beginTransaction();
          $mentor=mentors::create([
            'mentor_name'   =>  $request->mentor_name,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'is_active' =>  $isActive  
          ]);

          $mentor->subjects()->attach($request->spesialization_id);

          logActivity('Menambah Mentor Baru', 'Mentor: ' . $request->mentor_name);

          db::commit();
          return redirect()->route('admin.mentor.index')->with('success', 'Mentor berhasil ditambahkan.');
            
        }catch (\Exception $e) {
            db::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $mentor = mentors::with('subjects')->findOrFail($id);
        $subjects = subjects::all();
        
        $selectedSubjects = $mentor->subjects->pluck('id')->toArray();
        return view('Admin.mentor.update', compact('mentor', 'subjects', 'selectedSubjects'));
    }

    public function update(Request $request, $id)
    {
        $mentor = mentors::findOrFail($id);
        
        $request->validate([
            'mentor_name'    => 'required',
            'phone_number'   => 'required',
            'spesialization_id' => 'required|array', // Pastikan mapel dikirim sebagai array
        ]);

        $isActive = $request->has('isActive') ? 1 : 0;

        try {
            DB::beginTransaction();

            $mentor->update([
                'mentor_name'  => $request->mentor_name,
                'gender'       => $request->gender,
                'phone_number' => $request->phone_number,
                'is_active'    => $isActive
            ]);

            $mentor->subjects()->detach();

            $mentor->subjects()->attach($request->spesialization_id);

            logActivity('Mengubah Data Mentor', 'Mentor: ' . $request->mentor_name);

            DB::commit();
            return redirect()->route('admin.mentor.index')->with('success', 'Data mentor berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }
    public function destroy(mentors $mentor)
    {
        try {
            $mentor_name = $mentor->mentor_name;
            $mentor->delete();
            logActivity('Menghapus Mentor', 'Mentor: ' . $mentor_name);
            
            return response()->json([
                'success' => true,
                'message' => 'Data Mentor dan data terkait (Jadwal, dll) berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Mentor: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        $mentor = mentors::with('subjects')->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'mentor_name'   => $mentor->mentor_name,
                'gender'        => $mentor->gender,
                'phone_number'  => $mentor->phone_number,
                'specialization'=> $mentor->subjects->map(function($subject) {
                    return $subject->mapel_name;
                }),
                'is_active'     => $mentor->is_active,
                'created_at'    => $mentor->created_at->format('d M Y H:i'), // Format lebih manusiawi
                'updated_at'    => $mentor->updated_at->toISOString(),
            ]);
        }

        return abort(404);
    }
}
