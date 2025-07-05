<?php

namespace App\Http\Controllers\Api;

use App\Models\ExitProduct;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ExitProductController extends Controller
{
    //
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'product_id'   => 'required|exists:products,id',
    //         'exp_date'     => 'required|date',
    //         'removed_stock' => 'required|integer|min:1',
    //     ]);

    //     // Cek apakah stok cukup sebelum mengurangi
    //     $productStock = ProductStock::where([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date
    //     ])->first();

    //     if (!$productStock || $productStock->stock < $request->removed_stock) {
    //         return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi'], 400);
    //     }

    //     // Kurangi stok dari tabel product_stocks
    //     $productStock->decrement('stock', $request->removed_stock);

    //     // Tambahkan ke tabel exit_products
    //     $exit = ExitProduct::create($validated);

    //     return response()->json(['success' => true, 'message' => 'Stok berhasil dikurangi', 'data' => $exit], 201);
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'product_id'   => 'required|exists:products,id',
    //         'exp_date'     => 'required|date',
    //         'removed_stock' => 'required|integer|min:1',
    //     ]);

    //     // Ambil stok saat ini sebelum barang keluar
    //     $productStock = ProductStock::where([
    //         'product_id' => $request->product_id,
    //         'exp_date'   => $request->exp_date
    //     ])->first();

    //     $previousStock = $productStock ? $productStock->stock : 0;

    //     if ($previousStock < $request->removed_stock) {
    //         return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi'], 400);
    //     }

    //     $currentStock = $previousStock - $request->removed_stock;

    //     // Tambahkan ke tabel exit_products
    //     $exit = ExitProduct::create([
    //         'product_id'     => $request->product_id,
    //         'exp_date'       => $request->exp_date,
    //         'removed_stock'  => $request->removed_stock,
    //         'previous_stock' => $previousStock,
    //         'current_stock'  => $currentStock,
    //     ]);

    //     // Kurangi stok di product_stocks
    //     $productStock->update(['stock' => $currentStock]);

    //     return response()->json(['success' => true, 'message' => 'Stok berhasil dikurangi', 'data' => $exit], 201);
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'exp_date' => 'required|date',
    //         'removed_stock' => 'required|integer|min:1',
    //     ]);

    //     // Ambil stok saat ini sebelum dikurangi
    //     $productStock = ProductStock::where([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date
    //     ])->first();

    //     if (!$productStock || $productStock->stock <= 3) {
    //         return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi (stok saat ini 3 atau kurang)'], 400);
    //     }

    //     if ($productStock->stock < $request->removed_stock) {
    //         return response()->json(['success' => false, 'message' => 'Jumlah pengurangan melebihi stok yang tersedia'], 400);
    //     }

    //     // Simpan stok sebelum perubahan
    //     $previousStock = $productStock->stock;

    //     // Kurangi stok
    //     $productStock->decrement('stock', $request->removed_stock);

    //     // Simpan stok setelah perubahan
    //     $currentStock = $previousStock - $request->removed_stock;

    //     // Simpan data ke exit_products
    //     $exit = ExitProduct::create([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date,
    //         'removed_stock' => $request->removed_stock,
    //         'previous_stock' => $previousStock,
    //         'current_stock' => $currentStock,
    //     ]);

    //     return response()->json(['success' => true, 'message' => 'Stok berhasil dikurangi', 'data' => $exit], 201);
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'exp_date' => 'required|date',
            'removed_stock' => 'required|integer|min:1',
        ]);

        // Ambil stok saat ini sebelum dikurangi
        $productStock = ProductStock::where([
            'product_id' => $request->product_id,
            'exp_date' => $request->exp_date
        ])->first();

        if (!$productStock) {
            return response()->json(['success' => false, 'message' => 'Stok produk tidak ditemukan'], 400);
        }

        // // Cek apakah pengurangan akan menyebabkan stok dibawah 0 atau minus
        if ($productStock->stock - $request->removed_stock < 0) {
            return response()->json(['success' => false, 'message' => 'Pengurangan stok tidak dapat dilakukan karena akan menyebabkan jumlah stok dibawah 0'], 400);
        }

        // Simpan stok sebelum perubahan
        $previousStock = $productStock->stock;
        
        // Kurangi stok
        $productStock->decrement('stock', $request->removed_stock);
        
        // Simpan stok setelah perubahan
        $currentStock = $previousStock - $request->removed_stock;

        // Simpan data ke exit_products
        $exit = ExitProduct::create([
            'product_id' => $request->product_id,
            'exp_date' => $request->exp_date,
            'removed_stock' => $request->removed_stock,
            'previous_stock' => $previousStock,
            'current_stock' => $currentStock,
        ]);

        return response()->json(['success' => true, 'message' => 'Stok berhasil dikurangi', 'data' => $exit], 201);
    }
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => ExitProduct::with('product')->latest()->get()
        ]);
    }

    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'product_id'   => 'required|exists:products,id',
    //         'exp_date'     => 'required|date',
    //         'removed_stock' => 'required|integer|min:1',
    //     ]);

    //     $exit = ExitProduct::findOrFail($id);

    //     // Tambahkan kembali stok lama sebelum update
    //     ProductStock::where([
    //         'product_id' => $exit->product_id,
    //         'exp_date' => $exit->exp_date,
    //     ])->increment('stock', $exit->removed_stock);

    //     // Kurangi stok baru dari tabel product_stocks
    //     $productStock = ProductStock::where([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date
    //     ])->first();

    //     if (!$productStock || $productStock->stock < $request->removed_stock) {
    //         return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi'], 400);
    //     }

    //     $productStock->decrement('stock', $request->removed_stock);

    //     // Update entry
    //     $exit->update($validated);

    //     return response()->json(['success' => true, 'message' => 'Entry produk keluar berhasil diperbarui', 'data' => $exit]);
    // }

    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'exp_date' => 'required|date',
    //         'removed_stock' => 'required|integer|min:1',
    //     ]);

    //     $exit = ExitProduct::findOrFail($id);

    //     // Ambil stok saat ini sebelum update
    //     $productStock = ProductStock::where([
    //         'product_id' => $exit->product_id,
    //         'exp_date' => $exit->exp_date,
    //     ])->first();

    //     $previousStock = $productStock ? $productStock->stock : 0;

    //     // Kembalikan stok lama sebelum update
    //     ProductStock::where([
    //         'product_id' => $exit->product_id,
    //         'exp_date' => $exit->exp_date,
    //     ])->increment('stock', $exit->removed_stock);

    //     // Cek stok terbaru setelah pengembalian
    //     $updatedProductStock = ProductStock::where([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date,
    //     ])->first();

    //     if (!$updatedProductStock || $updatedProductStock->stock < $request->removed_stock) {
    //         return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi'], 400);
    //     }

    //     // Hitung stok baru setelah perubahan
    //     $currentStock = $updatedProductStock->stock - $request->removed_stock;

    //     // Update entry
    //     $exit->update([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date,
    //         'removed_stock' => $request->removed_stock,
    //         'previous_stock' => $previousStock,
    //         'current_stock' => $currentStock,
    //     ]);

    //     // Update stok di product_stocks
    //     $updatedProductStock->update(['stock' => $currentStock]);

    //     return response()->json(['success' => true, 'message' => 'Entry produk keluar berhasil diperbarui', 'data' => $exit]);
    // }

    // public function update(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'exp_date' => 'required|date',
    //         'removed_stock' => 'required|integer|min:1',
    //     ]);

    //     $exit = ExitProduct::findOrFail($id);

    //     // Kembalikan stok lama sebelum update
    //     ProductStock::where([
    //         'product_id' => $exit->product_id,
    //         'exp_date' => $exit->exp_date,
    //     ])->increment('stock', $exit->removed_stock);

    //     // Ambil stok terbaru setelah pengembalian
    //     $productStock = ProductStock::where([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date,
    //     ])->first();

    //     if (!$productStock || $productStock->stock <= 3) {
    //         return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi (stok saat ini 3 atau kurang)'], 400);
    //     }

    //     if ($productStock->stock < $request->removed_stock) {
    //         return response()->json(['success' => false, 'message' => 'Jumlah pengurangan melebihi stok yang tersedia'], 400);
    //     }

    //     // Simpan stok sebelum perubahan
    //     $previousStock = $productStock->stock;

    //     // Kurangi stok
    //     $productStock->decrement('stock', $request->removed_stock);

    //     // Simpan stok setelah perubahan
    //     $currentStock = $previousStock - $request->removed_stock;

    //     // Update entry
    //     $exit->update([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date,
    //         'removed_stock' => $request->removed_stock,
    //         'previous_stock' => $previousStock,
    //         'current_stock' => $currentStock,
    //     ]);

    //     return response()->json(['success' => true, 'message' => 'Entry produk keluar berhasil diperbarui', 'data' => $exit]);
    // }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'exp_date' => 'required|date',
            'removed_stock' => 'required|integer|min:1',
        ]);

        $exit = ExitProduct::findOrFail($id);

        // Kembalikan stok lama sebelum update
        ProductStock::where([
            'product_id' => $exit->product_id,
            'exp_date' => $exit->exp_date,
        ])->increment('stock', $exit->removed_stock);

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
        if ($productStock->stock - $request->removed_stock < 0) {
            return response()->json(['success' => false, 'message' => 'Pengurangan stok tidak dapat dilakukan karena akan menyebabkan stok dibawah 0'], 400);
        }

        // Simpan stok sebelum perubahan
        $previousStock = $productStock->stock;

        // Kurangi stok
        $productStock->decrement('stock', $request->removed_stock);

        // Simpan stok setelah perubahan
        $currentStock = $previousStock - $request->removed_stock;

        // Update entry
        $exit->update([
            'product_id' => $request->product_id,
            'exp_date' => $request->exp_date,
            'removed_stock' => $request->removed_stock,
            'previous_stock' => $previousStock,
            'current_stock' => $currentStock,
        ]);

        return response()->json(['success' => true, 'message' => 'Entry produk keluar berhasil diperbarui', 'data' => $exit]);
    }

    public function destroy($id)
    {
        $exit = ExitProduct::findOrFail($id);

        // Tambahkan kembali stok yang sebelumnya dikurangi
        ProductStock::where([
            'product_id' => $exit->product_id,
            'exp_date' => $exit->exp_date,
        ])->increment('stock', $exit->removed_stock);

        // Hapus entry dari database
        $exit->delete();

        return response()->json(['success' => true, 'message' => 'Entry produk keluar berhasil dihapus']);
    }
}
