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
        })->paginate(5) 
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
            'monthly_price' => 'required|numeric',
            'description' => 'nullable',
        ]);

        subjects::create([
            'mapel_name' => $request->mapel_name,
            'category_id' => $request->category_id,
            'monthly_price' => $cleanPrice,
            'description' => $request->description,
        ]);

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
            'monthly_price' => 'required|numeric',
            'description' => 'nullable',
        ]);

        $subject->update([
            'mapel_name' => $request->mapel_name,
            'category_id' => $request->category_id,
            'monthly_price' => $cleanPrice,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }
    public function destroy(subjects $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted successfully.');
    }
    public function show($id)
    {
        $subject = subjects::findOrFail($id);
        // Cek jika request datang dari AJAX/Fetch
        if (request()->ajax()) {
            return response()->json([
                'subject_name'   => $subject->mapel_name,
                'category_name'  => $subject->categories->category_name,
                'description'      => $subject->description,
                'monthly_price'      => $subject->monthly_price,
                // Format tanggal agar bagus dibaca JS
                'created_at' => $subject->created_at->toISOString(),
                'updated_at' => $subject->updated_at->toISOString(),
            ]);
        }

        // Jika diakses biasa (bukan AJAX), bisa diarahkan ke view lain atau abort
        return abort(404);
    }
        
}
