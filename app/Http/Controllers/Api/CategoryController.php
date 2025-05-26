<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories',
            'code' => 'required|unique:categories|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->all());

        return response()->json($category, 201);
    }

    //// public function show($id)
    // {
    //     $category = Category::with('products')->findOrFail($id);
    //     return response()->json($category);
    // }

    public function show($id)
    {
        $category = Category::with(['products.stocks'])->findOrFail($id);

        // Hitung total stok untuk setiap produk
        $category->products->each(function ($product) {
            $product->total_stock = $product->stocks->sum('stock'); // Total stok semua exp_date
            unset($product->stocks); // Hapus informasi detail stok per exp_date
        });

        return response()->json($category);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
            'code' => 'required|unique:categories,code,' . $id . '|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if ($category->products()->count() > 0) {
            return response()->json(['error' => 'Kategori tidak bisa dihapus karena masih digunakan oleh produk!'], 400);
        }

        $category->delete();
        return response()->json(null, 204);
    }
}
