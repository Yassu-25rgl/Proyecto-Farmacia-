<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    // READ: listado
    public function index()
    {
        $categories = Category::all();
        return view('settings.categories.index', compact('categories'));
    }

    // Formulario vacío
    public function create()
    {
        return view('settings.categories.create');
    }

    // CREATE: validar, guardar, redirigir
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:categories',
            'description' => 'nullable|max:255',
        ]);

        Category::create($validated);

        return to_route('categories.index')
            ->with('ok', 'Categoría registrada correctamente.');
    }

    // Formulario lleno (route model binding)
    public function edit(Category $category)
    {
        return view('settings.categories.edit', compact('category'));
    }

    // UPDATE: validar, actualizar, redirigir
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'max:50',
                Rule::unique('categories', 'name')->ignore($category->id)
            ],
            'description' => 'nullable|max:255',
        ]);

        $category->update($validated);

        return to_route('categories.index')
            ->with('ok', 'Categoría actualizada correctamente.');
    }

    // DELETE: respeta restrictOnDelete de medicines
    public function destroy(Category $category)
    {
        if ($category->medicines()->exists()) {
            return to_route('categories.index')
                ->with('error', 'No se puede eliminar: tiene medicamentos asociados.');
        }

        $category->delete();

        return to_route('categories.index')
            ->with('ok', 'Categoría eliminada correctamente.');
    }
}