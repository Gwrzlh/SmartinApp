<?php

namespace App\Http\Controllers;

use App\Models\categories;
use Illuminate\Http\Request;
use App\Models\EnrollmentSchedule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $categories = categories::when($search, function ($query) use ($search) {
            return $query->where('category_name', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(5)
        ->withQueryString();

        return view('Admin.Category.index', compact('categories'));
    }
    public function create()
    {
        return view('Admin.Category.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required',
        ]);

        categories::create([
            'category_name' => $request->category_name,
        ]);

        logActivity('Membuat Category Baru','Category: '.$request->category_name);
        return redirect()->route('admin.category.index')->with('success', 'Category created successfully.');
    }
    public function edit(categories $category)
    {
        return view('Admin.Category.update', compact('category'));
    }
    public function update(Request $request, categories $category)
    {
        $request->validate([
            'category_name' => 'required|unique:categories,category_name,' . $category->id,
        ]);

        $category->update([
            'category_name' => $request->category_name,
        ]);

        logActivity('Melakukan Update Category','Category: '.$request->category_name);
        return redirect()->route('admin.category.index')->with('success', 'Category updated successfully.');
    }
    public function destroy(categories $category)
    {
        try {
            $categoryName = $category->category_name;
            $category->delete();

            logActivity('Menghapus Category', ' Category: ' . $categoryName);
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus secara permanen.'
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            // Cek jika error disebabkan oleh Restrict Constraint
            if ($e->getCode() === "23000" || str_contains($e->getMessage(), '1451')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus: Kategori ini masih digunakan oleh data Mata Pelajaran. Silakan hapus atau pindahkan mata pelajaran terkait terlebih dahulu.'
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
}
