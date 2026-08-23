<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // 100 por defecto para categorías, ya que suelen ser filtros y necesitamos todos.
        $perPage = $request->input('per_page', 50); 
        
        $categories = Category::paginate($perPage);
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name'
        ]);

        $category = Category::create($validated);

        return response()->json(['message' => 'Category created.', 'data' => $category], 201);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id
        ]);

        $category->update($validated);

        return response()->json(['message' => 'Updated Category.', 'data' => $category], 200);
    }

    public function destroy(Category $category)
    {
        // Validación de integridad: No borrar si tiene proyectos
        if ($category->projects()->count() > 0) {
            return response()->json(['error' => 'You can’t delete a category that has assigned projects.'], 422);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted'], 200);
    }
}