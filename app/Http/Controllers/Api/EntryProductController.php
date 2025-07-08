<?php

namespace App\Http\Controllers\Api;

use App\Models\EntryProduct;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EntryProductController extends Controller
{
    //
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

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'exp_date' => 'required|date',
    //         'added_stock' => 'required|integer|min:1',
    //     ]);

    //     // Ambil stok saat ini sebelum barang masuk
    //     $productStock = ProductStock::where([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date
    //     ])->first();

    //     $previousStock = $productStock ? $productStock->stock : 0;
    //     $currentStock = $previousStock + $request->added_stock;

    //     // Tambahkan ke tabel entry_products
    //     $entry = EntryProduct::create([
    //         'product_id' => $request->product_id,
    //         'exp_date' => $request->exp_date,
    //         'added_stock' => $request->added_stock,
    //         'previous_stock' => $previousStock,
    //         'current_stock' => $currentStock,
    //     ]);

    //     // Update stok di product_stocks
    //     ProductStock::updateOrCreate(
    //         ['product_id' => $request->product_id, 'exp_date' => $request->exp_date],
    //         ['stock' => $currentStock]
    //     );

    //     return response()->json(['success' => true, 'message' => 'Stok berhasil ditambahkan', 'data' => $entry], 201);
    // }

    // Untuk menambah data stok barang masuk
    public function store(Request $request)
    {
        // Validasi yang lebih fleksibel: exp_date atau new_exp_date harus ada, tapi tidak keduanya.
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'added_stock' => 'required|integer|min:1',
            'exp_date' => 'required_without:new_exp_date|nullable|date',
            'new_exp_date' => 'required_without:exp_date|nullable|date|after_or_equal:today',
        ]);

        $expDateToUse = '';
        $previousStock = 0;

        // Skenario 1: Pengguna membuat tanggal expired baru
        if ($request->has('new_exp_date') && !empty($request->new_exp_date)) {
            $expDateToUse = $request->new_exp_date;

            // Pastikan tanggal baru ini belum ada untuk produk yang sama untuk menghindari duplikat
            $existingStock = ProductStock::where('product_id', $request->product_id)
                ->where('exp_date', $expDateToUse)
                ->exists();
            if ($existingStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal expired yang Anda masukkan sudah ada untuk produk ini. Silakan pilih dari daftar.'
                ], 422); // Unprocessable Entity
            }

            // Karena ini tanggal baru, stok sebelumnya pasti 0
            $previousStock = 0;

            // Skenario 2: Pengguna memilih tanggal expired yang sudah ada
        } else {
            $expDateToUse = $request->exp_date;

            // Ambil stok saat ini dari tanggal yang dipilih
            $productStock = ProductStock::where('product_id', $request->product_id)
                ->where('exp_date', $expDateToUse)
                ->first();

            $previousStock = $productStock ? $productStock->stock : 0;
        }

        // Lanjutkan dengan logika yang sama untuk kedua skenario
        $currentStock = $previousStock + $request->added_stock;

        // Tambahkan ke tabel riwayat (entry_products)
        $entry = EntryProduct::create([
            'product_id' => $request->product_id,
            'exp_date' => $expDateToUse,
            'added_stock' => $request->added_stock,
            'previous_stock' => $previousStock,
            'current_stock' => $currentStock,
        ]);

        // Buat atau perbarui stok di tabel utama (product_stocks)
        ProductStock::updateOrCreate(
            ['product_id' => $request->product_id, 'exp_date' => $expDateToUse],
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

    // Menampilkan data barang masuk
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

    // Untuk mengubah data barang masuk
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

    // Untuk menghapus data barang masuk
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
