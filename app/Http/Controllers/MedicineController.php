<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicineController extends Controller
{
    // Unidades a partir de las cuales el stock (amount) se considera bajo
    const LOW_STOCK = 10;

    public function index(Request $request)
    {
        $medicines = Medicine::with('category')
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->boolean('low_stock'), function ($query) {
                $query->where('amount', '<=', self::LOW_STOCK);
            })
            ->orderByDesc('id')
            ->get();

        $categories = Category::orderBy('name')->get();
        $lowStock = self::LOW_STOCK;

        return view('management.medicines.index', compact('medicines', 'categories', 'lowStock'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('management.medicines.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'integer', 'min:0'],
            'price_unit' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data['subtotal'] = round($data['amount'] * $data['price_unit'], 2);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('medicines', 'public');
        }

        Medicine::create($data);

        return redirect()
            ->route('medicines.index')
            ->with('ok', 'Medicamento registrado correctamente.');
    }

    public function edit(Medicine $medicine)
    {
        $categories = Category::orderBy('name')->get();

        return view('management.medicines.edit', compact('medicine', 'categories'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'integer', 'min:0'],
            'price_unit' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data['subtotal'] = round($data['amount'] * $data['price_unit'], 2);

        if ($request->hasFile('image')) {
            if ($medicine->image) {
                Storage::disk('public')->delete($medicine->image);
            }
            $data['image'] = $request->file('image')->store('medicines', 'public');
        } else {
            unset($data['image']);
        }

        $medicine->update($data);

        return redirect()
            ->route('medicines.index')
            ->with('ok', 'Medicamento actualizado correctamente.');
    }

    public function destroy(Medicine $medicine)
    {
        if ($medicine->image) {
            Storage::disk('public')->delete($medicine->image);
        }

        $medicine->delete();

        return redirect()
            ->route('medicines.index')
            ->with('ok', 'Medicamento eliminado correctamente.');
    }
}