<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\bundlings;
use App\Models\bundling_details;
use App\Models\subjects;
use Illuminate\Support\Facades\DB;

class bundlingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $bundlings = bundlings::when($search, function ($query) use ($search) {
            return $query->where('bundling_name', 'like', "%{$search}%");
        })
        ->paginate(5)
        ->withQueryString();

        return view('Admin.bundlings.index', compact('bundlings'));
    }
    public function create()
    {
        $subjects = subjects::all();
        return view('Admin.bundlings.create', compact('subjects'));
    }
    public function store(Request $request)
    {
       $request->validate([
            'bundling_name'=>'required',
            'description'=>'required',
            'bundling_price'=>'required|numeric',
            'subjects_id'=>'required|array',
       ]);

       $isActive = $request->has('isActive') ? 1 : 0;
       $cleanPrice = str_replace('.', '', $request->bundling_price);
    
       try{
         db::beginTransaction();
            $bundling = bundlings::create([
                'bundling_name' => $request->bundling_name,
                'description' => $request->description,
                'bundling_price' => $cleanPrice,
                'is_active' => $isActive,
            ]);
            foreach ($request->subjects_id as $subject_id) {
                bundling_details::create([
                    'bundling_id' => $bundling->id,
                    'subject_id' => $subject_id,
                ]);
            }
            db::commit();
            logActivity('Membuat Bundling Baru','   Bundling : '.$request->bundling_name);
            return redirect()->route('admin.bundling.index')->with('success', 'Bundling created successfully.');
        } catch (\Exception $e) {
            db::rollback();
            throw $e;
        }
    }
    public function edit(bundlings $bundling)
    {
        $subjects = subjects::all();
        $selectedSubjects = $bundling->details->pluck('subject_id')->toArray();
        return view('Admin.bundlings.update', compact('bundling', 'subjects', 'selectedSubjects'));
    }
    public function update(Request $request, bundlings $bundling)
    {
        $bundling = bundlings::findOrFail($bundling->id);

        $isActive = $request->has('isActive') ? 1 : 0;
       $cleanPrice = str_replace('.', '', $request->bundling_price);

        try{
            db::beginTransaction();
            $bundling->update([
                'bundling_name' => $request->bundling_name,
                'description' => $request->description,
                'bundling_price' => $cleanPrice,
                'is_active' => $isActive,
            ]);
            $bundling->details()->delete();

            foreach ($request->subjects_id as $subject_id) {
                bundling_details::create([
                    'bundling_id' => $bundling->id,
                    'subject_id' => $subject_id,
                ]);
            }
            logActivity('Melakukan Update Bundling','   Bundling : '.$request->bundling_name);
            db::commit();
            return redirect()->route('admin.bundling.index')->with('success', 'Bundling updated successfully.');
        } catch (\Exception $e) {
            db::rollback();
            throw $e;
            return back()->with('error', 'Gagal memperbarui data.');
        }
    }
    public function destroy(bundlings $bundling)
    {
        $bundling->delete();
        logActivity('Menghapus Bundling');
        return redirect()->route('admin.bundling.index')->with('success', 'Bundling deleted successfully.');
    }
    public function show($id)
    {
        $bundling = bundlings::findOrFail($id);

        logActivity('Melihat Detail Bundling','   Bundling : '.$bundling->bundling_name);

        if (request()->ajax()) {
            return response()->json([
                'bundling_name'   => $bundling->bundling_name,
                'description'  => $bundling->description,
                'price'      => $bundling->bundling_price,
                'is_active'  => $bundling->is_active,
                'subjects'   => $bundling->details->map(function($detail) {
                    return $detail->subject ? $detail->subject->mapel_name : 'N/A';
                })->toArray(),
                'created_at' => $bundling->created_at->toISOString(),
                'updated_at' => $bundling->updated_at->toISOString(),
            ]);
        }

        return abort(404);
    }
}
