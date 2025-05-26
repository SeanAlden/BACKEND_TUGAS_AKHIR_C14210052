<?php

namespace App\Http\Controllers\Api;

use App\Models\EntryProduct;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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

    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'exp_date' => 'required|date',
    //         'added_stock' => 'required|integer|min:1',
    //     ]);

    //     $entry = EntryProduct::findOrFail($id);

    //     // Ambil stok saat ini sebelum update
    //     $productStock = ProductStock::where([
    //         'product_id' => $entry->product_id,
    //         'exp_date' => $entry->exp_date
    //     ])->first();

    //     $previousStock = $productStock ? $productStock->stock : 0;

    //     // Kembalikan stok lama sebelum update
    //     ProductStock::where([
    //         'product_id' => $entry->product_id,
    //         'exp_date' => $entry->exp_date,
    //     ])->decrement('stock', $entry->added_stock);

    //     // Hitung stok baru setelah perubahan
    //     $updatedProductStock = ProductStock::where([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date,
    //     ])->first();

    //     $currentStock = $updatedProductStock ? $updatedProductStock->stock + $request->added_stock : $request->added_stock;

    //     // Update entry
    //     $entry->update([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date,
    //         'added_stock' => $request->added_stock,
    //         'previous_stock' => $previousStock,
    //         'current_stock' => $currentStock,
    //     ]);

    //     // Tambahkan stok baru ke product_stocks
    //     ProductStock::updateOrCreate(
    //         ['product_id' => $request->product_id, 'exp_date' => $request->exp_date],
    //         ['stock' => $currentStock]
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

        // Kembalikan stok lama sebelum update
        ProductStock::where([
            'product_id' => $entry->product_id,
            'exp_date' => $entry->exp_date,
        ])->decrement('stock', $entry->added_stock);

        // Ambil stok terbaru setelah pengembalian
        $productStock = ProductStock::where([
            'product_id' => $request->product_id,
            'exp_date' => $request->exp_date,
        ])->first();

        if (!$productStock) {
            return response()->json(['success' => false, 'message' => 'Stok produk tidak ditemukan'], 400);
        }

        // Cek apakah stok sebelum dikurangi 10 atau kurang
        // if ($productStock->stock <= 10) {
        //     return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi (stok saat ini 10 atau kurang)'], 400);
        // }

        // Cek apakah pengurangan akan menyebabkan stok negatif atau 0
        // if ($productStock->stock + $request->added_stock < 0) {
        //     return response()->json(['success' => false, 'message' => 'Pengurangan stok tidak dapat dilakukan karena akan menyebabkan stok dibawah 0'], 400);
        // }

        // Simpan stok sebelum perubahan
        $previousStock = $productStock->stock;

        // Kurangi stok
        $productStock->increment('stock', $request->added_stock);

        // Simpan stok setelah perubahan
        $currentStock = $previousStock + $request->added_stock;

        // Update entry
        $entry->update([
            'product_id' => $request->product_id,
            'exp_date' => $request->exp_date,
            'added_stock' => $request->added_stock,
            'previous_stock' => $previousStock,
            'current_stock' => $currentStock,
        ]);

        return response()->json(['success' => true, 'message' => 'Entry produk keluar berhasil diperbarui', 'data' => $entry]);
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
