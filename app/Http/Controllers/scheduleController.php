<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\schedules;
use App\Models\subjects;
use App\Models\students;
use App\Models\mentors;
use App\Models\bundlings;

class scheduleController extends Controller
{
    public function index(Request $request)
    {
        $subjects = subjects::all(); // Tetap ambil untuk keperluan lain jika perlu

        $schedules = schedules::with(['subject', 'mentor', 'bundling'])
            ->withCount('enrollments') 
                ->when($request->search, function ($query) use ($request) {
                    $search = $request->search;
                    return $query->where(function($mainQuery) use ($search) {
                        $mainQuery->whereHas('subject', function($q) use ($search) {
                            $q->where('mapel_name', 'like', "%{$search}%");
                        })->orWhereHas('mentor', function($q) use ($search) {
                            $q->where('mentor_name', 'like', "%{$search}%");
                        })->orWhereHas('bundling', function($q) use ($search) {
                            $q->where('bundling_name', 'like', "%{$search}%");
                        });
                    });
                })
            ->latest()
            ->paginate(5);

        return view('Admin.schedules.index', compact('schedules', 'subjects'));
    }

    public function create()
    {
        // Pastikan kirim data bundling ke view
        $bundlings = \App\Models\bundlings::where('is_active', true)->get();
        return view('Admin.schedules.create', compact('bundlings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required',
            'mentor_id' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruangan' => 'required',
            // 'capacity' => 'required|integer|min:1',
        ]);

        // Cek Bentrok Mentor (Mentor tidak boleh mengajar di jam yang sama)
        $isBentrok = schedules::where('mentor_id', $request->mentor_id)
            ->where('hari', $request->hari)
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                    ->where('jam_selesai', '>', $request->jam_mulai);
                });
            })->exists();

        if ($isBentrok) {
            return back()->with('error', 'Jadwal bentrok! Mentor tersebut sudah memiliki jadwal di jam yang sama.')->withInput();
        }

        schedules::create($request->all());

        logActivity('Menambah Jadwal Kelas', 'Hari: ' . $request->hari . ' Jam: ' . $request->jam_mulai);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dibuat!');
    }
    public function edit($id)
    {
        $schedule = schedules::findOrFail($id);
        $bundlings = \App\Models\bundlings::where('is_active', true)->get();
        
        $selected_bundling = \App\Models\bundlings::whereHas('subjects', function($q) use ($schedule) {
            $q->where('subjects.id', $schedule->subject_id);
        })->first();
        $bundling_id = $selected_bundling ? $selected_bundling->id : null;

        return view('Admin.schedules.update', compact('schedule', 'bundlings', 'bundling_id'));
    }

    public function update(Request $request, $id)
    {
        $schedule = schedules::findOrFail($id);
        $request->validate([
            'subject_id' => 'required',
            'mentor_id' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruangan' => 'required',
            // 'capacity' => 'required|integer|min:1',
        ]);

        // check conflict excluding the current schedule
        $isBentrok = schedules::where('mentor_id', $request->mentor_id)
            ->where('hari', $request->hari)
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                      ->where('jam_selesai', '>', $request->jam_mulai);
                });
            })
            ->where('id', '<>', $id)
            ->exists();

        if ($isBentrok) {
            return back()->with('error', 'Jadwal bentrok! Mentor tersebut sudah memiliki jadwal di jam yang sama.')->withInput();
        }

        $schedule->update($request->all());

        logActivity('Mengubah Data Jadwal Kelas', 'Hari: ' . $request->hari . ' Jam: ' . $request->jam_mulai);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function getSubjectsByMentor($mentorId)
    {
        // Asumsi: Mentor memiliki relasi 'subjects' melalui tabel pivot
        $mentor = \App\Models\mentors::with('subjects')->findOrFail($mentorId);
        
        return response()->json($mentor->subjects);
    }
    public function show($id)
    {
        $schedule = schedules::with(['subject', 'mentor', 'bundling'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'mentor_name'   => $schedule->mentor->mentor_name ?? 'N/A',
                'subject_name'  => $schedule->subject->mapel_name ?? 'N/A',
                'bundling_name' => $schedule->bundling->bundling_name ?? 'Reguler/Lainnya',
                'hari'          => $schedule->hari,
                'jam'           => $schedule->jam_mulai . ' - ' . $schedule->jam_selesai,
                'ruangan'       => $schedule->ruangan,
                'created_at'    => $schedule->created_at,
                'updated_at'    => $schedule->updated_at,
            ]);
        }

        return abort(404);
    }
    public function destroy($id)
    {
        try {
            $schedule = schedules::findOrFail($id);
            $schedule->delete();

            logActivity('Menghapus Jadwal Kelas');

            return response()->json([
                'success' => true,
                'message' => 'Jadwal dan data terkait berhasil dihapus secara permanen.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
   public function getSubjectsByBundling($bundlingId)
{
    // $bundling = bundlings::with('subjects')->find($bundlingId);
    $bundling = bundlings::where('id', $bundlingId)->first();
    
    if (!$bundling) {
        return response()->json([]); // Kembalikan array kosong jika tidak ketemu
    }

    // Ambil hanya array subjects-nya saja
    return response()->json($bundling->subjects); 
}

public function getMentorsBySubject($subjectId)
{
    // $subject = \App\Models\subjects::with('mentors')->find($subjectId);
    $subject = subjects::where('id', $subjectId)->first();  

    if (!$subject) {
        return response()->json([]); 
    }

    // Ambil hanya array mentors-nya saja
    return response()->json($subject->mentors);
}
}
