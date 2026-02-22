<?php

namespace App\Http\Controllers;

use App\Models\categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $categories = categories::when($search, function ($query) use ($search) {
            return $query->where('category_name', 'like', "%{$search}%");
        })
        ->paginate(5) // Menampilkan 10 data per halaman
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

        return redirect()->route('admin.category.index')->with('success', 'Category updated successfully.');
    }
    public function destroy(categories $category)
    {
        
        $category->subjects()->delete(); 
        $category->delete();

        return redirect()->route('admin.category.index')
            ->with('success', 'Kategori dan semua Mapel di dalamnya berhasil dihapus.');
    }
}
