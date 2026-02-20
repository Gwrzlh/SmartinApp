<?php

namespace App\Http\Controllers;

use App\Models\categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('Admin.Category.index');
    }
    public function create()
    {
        return view('Admin.Category.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
        ]);

        categories::create([
            'name' => $request->name,
        ]);

        return redirect()->route('Admin.Category.index')->with('success', 'Category created successfully.');
    }
    public function edit(categories $category)
    {
        return view('Admin.Category.edit', compact('category'));
    }
    public function update(Request $request, categories $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('Admin.Category.index')->with('success', 'Category updated successfully.');
    }
    public function destroy(categories $category)
    {
        $category->delete();
        return redirect()->route('Admin.Category.index')->with('success', 'Category deleted successfully.');
    }
}
