<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\subjects;
use App\Models\categories;

class SubjectsController extends Controller
{
    public function index(Request $request)
    {
        $categories = categories::all();
        $search = $request->get('search');
        $filterbycategory = $request->get('filterbycategory');

        $subjects = subjects::with('categories')
        ->when($search, function ($query) use ($search) {
            return $query->where('mapel_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
        })
        ->when($filterbycategory, function ($query) use ($filterbycategory) {
            return $query->where('category_id', $filterbycategory);
        })
        ->latest()
        ->paginate(5) 
        ->withQueryString(); 

        return view('Admin.Subjects.index', compact('subjects','categories'));
    }
    public function create()
    {
        $categories = categories::all();
        return view('Admin.Subjects.create', compact('categories'));
    }
    public function store(Request $request)
    {
        $cleanPrice = str_replace('.', '', $request->monthly_price);

        $request->validate([
            'mapel_name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
        ]);

        subjects::create([
            'mapel_name' => $request->mapel_name,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        logActivity('Menambah Mapel Baru', 'Mapel: ' . $request->mapel_name);

        return redirect()->route('admin.subjects.index')->with('success', 'Mapel created successfully.');
    }
    public function edit(Request $request, subjects $subject)
    {
        $categories = categories::all();
        return view('Admin.Subjects.update', compact('subject', 'categories'));
    }
    public function update(Request $request, subjects $subject)
    {
        $cleanPrice = str_replace('.', '', $request->monthly_price);

        $request->validate([
            'mapel_name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
        ]);

        $subject->update([
            'mapel_name' => $request->mapel_name,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        logActivity('Mengubah Data Mapel', 'Mapel: ' . $request->mapel_name);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }
    public function destroy(subjects $subject)
    {
        try {
            $mapel_name = $subject->mapel_name;
            $subject->delete();
            logActivity('Menghapus Mapel', 'Mapel: ' . $mapel_name);
            
            return response()->json([
                'success' => true,
                'message' => 'Mata Pelajaran berhasil dihapus.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === "23000" || str_contains($e->getMessage(), '1451')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus: Mata Pelajaran ini masih terdaftar dalam program Bundling. Silakan hapus program bundling terkait terlebih dahulu.'
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        $subject = subjects::findOrFail($id);
        if (request()->ajax()) {
            return response()->json([
                'subject_name'   => $subject->mapel_name,
                'category_name'  => $subject->categories->category_name,
                'description'      => $subject->description,
                'created_at' => $subject->created_at->toISOString(),
                'updated_at' => $subject->updated_at->toISOString(),
            ]);
        }

        return abort(404);
    }
        
}
