<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EntryProduct;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntryProductController extends Controller
{
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'exp_date'   => 'required|date',
    //         'added_stock' => 'required|integer|min:1',
    //     ]);

    //     // Tambahkan ke tabel entry_products
    //     $entry = EntryProduct::create($validated);

    //     // Tambahkan stok ke tabel product_stocks
    //     ProductStock::updateOrCreate(
    //         ['product_id' => $request->product_id, 'exp_date' => $request->exp_date],
    //         ['stock' => DB::raw("stock + {$request->added_stock}")]
    //     );

    //     return response()->json(['success' => true, 'message' => 'Stok berhasil ditambahkan', 'data' => $entry], 201);
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'exp_date' => 'required|date',
            'added_stock' => 'required|integer|min:1',
        ]);

        // Ambil stok saat ini sebelum barang masuk
        $productStock = ProductStock::where([
            'product_id' => $request->product_id,
            'exp_date' => $request->exp_date
        ])->first();

        $previousStock = $productStock ? $productStock->stock : 0;
        $currentStock = $previousStock + $request->added_stock;

        // Tambahkan ke tabel entry_products
        $entry = EntryProduct::create([
            'product_id' => $request->product_id,
            'exp_date' => $request->exp_date,
            'added_stock' => $request->added_stock,
            'previous_stock' => $previousStock,
            'current_stock' => $currentStock,
        ]);

        // Update stok di product_stocks
        ProductStock::updateOrCreate(
            ['product_id' => $request->product_id, 'exp_date' => $request->exp_date],
            ['stock' => $currentStock]
        );

        return response()->json(['success' => true, 'message' => 'Stok berhasil ditambahkan', 'data' => $entry], 201);
    }


    // public function index()
    // {
    //     return response()->json([
    //         'success' => true,
    //         'data' => EntryProduct::with('product')->latest()->get()
    //     ]);
    // }

    public function index()
    {
        $entries = EntryProduct::with('product')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $entries
        ]);
    }

    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'exp_date'   => 'required|date',
    //         'added_stock' => 'required|integer|min:1',
    //     ]);

    //     $entry = EntryProduct::findOrFail($id);

    //     // Update stok lama di product_stocks
    //     ProductStock::where([
    //         'product_id' => $entry->product_id,
    //         'exp_date' => $entry->exp_date,
    //     ])->decrement('stock', $entry->added_stock);

    //     // Update entry
    //     $entry->update($validated);

    //     // Tambahkan stok baru ke product_stocks
    //     ProductStock::updateOrCreate(
    //         ['product_id' => $request->product_id, 'exp_date' => $request->exp_date],
    //         ['stock' => DB::raw("stock + {$request->added_stock}")]
    //     );

    //     return response()->json(['success' => true, 'message' => 'Entry produk berhasil diperbarui', 'data' => $entry]);
    // }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'exp_date' => 'required|date',
            'added_stock' => 'required|integer|min:1',
        ]);

        $entry = EntryProduct::findOrFail($id);

        // Ambil stok saat ini sebelum update
        $productStock = ProductStock::where([
            'product_id' => $entry->product_id,
            'exp_date' => $entry->exp_date
        ])->first();

        $previousStock = $productStock ? $productStock->stock : 0;

        // Kembalikan stok lama sebelum update
        ProductStock::where([
            'product_id' => $entry->product_id,
            'exp_date' => $entry->exp_date,
        ])->decrement('stock', $entry->added_stock);

        // Hitung stok baru setelah perubahan
        $updatedProductStock = ProductStock::where([
            'product_id' => $request->product_id,
            'exp_date' => $request->exp_date,
        ])->first();

        $currentStock = $updatedProductStock ? $updatedProductStock->stock + $request->added_stock : $request->added_stock;

        // Update entry
        $entry->update([
            'product_id' => $request->product_id,
            'exp_date' => $request->exp_date,
            'added_stock' => $request->added_stock,
            'previous_stock' => $previousStock,
            'current_stock' => $currentStock,
        ]);

        // Tambahkan stok baru ke product_stocks
        ProductStock::updateOrCreate(
            ['product_id' => $request->product_id, 'exp_date' => $request->exp_date],
            ['stock' => $currentStock]
        );

        return response()->json(['success' => true, 'message' => 'Entry produk berhasil diperbarui', 'data' => $entry]);
    }


    public function destroy($id)
    {
        $entry = EntryProduct::findOrFail($id);

        // Kurangi stok dari product_stocks
        ProductStock::where([
            'product_id' => $entry->product_id,
            'exp_date' => $entry->exp_date,
        ])->decrement('stock', $entry->added_stock);

        // Hapus entry dari database
        $entry->delete();

        return response()->json(['success' => true, 'message' => 'Entry produk berhasil dihapus']);
    }
}
