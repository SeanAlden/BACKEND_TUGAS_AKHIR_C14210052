<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Favorite;
use App\Models\Notification;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ProductStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductStatusHistoryDetail;

class ProductController extends Controller
{
    // exp masing masing

    // public function index()
    // {
    //     // $products = Product::all();
    //     // $products = Product::with('category')->get();
    //     $products = Product::with('category')->withCount('transactionDetails')->get();
    //     return response()->json([
    //         'success' => true,
    //         'data' => $products
    //     ], 200);
    // }

    // public function index()
    // {
    //     // $products = Product::with(['category', 'stocks'])->get();
    //     $products = Product::with(['category', 'stocks'])->withCount('transactionDetails')->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $products
    //     ], 200);
    // }

    // public function index()
    // {
    //     $products = Product::with(['category', 'stocks'])->withCount('transactionDetails')->get();

    //     foreach ($products as $product) {
    //         foreach ($product->stocks as $stock) {
    //             // Format tanggal untuk pesan
    //             $expDateFormatted = Carbon::parse($stock->exp_date)->format('d-m-Y');

    //             // === 1. CEK SISA STOK DI BAWAH 7 ===
    //             if ($stock->stock < 7) {
    //                 $message = "{$product->name} dengan kadaluarsa {$expDateFormatted} tersisa {$stock->stock} stok lagi";

    //                 // Cek apakah notifikasi ini sudah ada
    //                 $exists = Notification::where('message', $message)
    //                     ->where('notification_type', 'Sisa Stok')
    //                     ->exists();

    //                 if (!$exists) {
    //                     Notification::create([
    //                         'message' => $message,
    //                         'notification_type' => 'Sisa Stok',
    //                     ]);
    //                 }
    //             }

    //             // === 2. CEK TANGGAL KADALUARSA SUDAH DEKAT (< 30 hari dari hari ini) ===
    //             $today = Carbon::today();
    //             $expDate = Carbon::parse($stock->exp_date);

    //             if ($expDate->diffInDays($today, false) <= 30 && $expDate->isFuture()) {
    //                 $message = "Pada {$product->name}, terdapat stok dengan tanggal expired {$expDateFormatted} yang sudah dekat";

    //                 // Cek apakah notifikasi ini sudah ada
    //                 $exists = Notification::where('message', $message)
    //                     ->where('notification_type', 'Tanggal Kadaluarsa')
    //                     ->exists();

    //                 if (!$exists) {
    //                     Notification::create([
    //                         'message' => $message,
    //                         'notification_type' => 'Tanggal Kadaluarsa',
    //                     ]);
    //                 }
    //             }
    //         }
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $products
    //     ], 200);
    // }

    public function index()
    {
        $products = Product::with(['category', 'stocks'])->withCount('transactionDetails')->get();

        foreach ($products as $product) {
            foreach ($product->stocks as $stock) {
                // 1. Cek jika stok di bawah 7
                if ($stock->stock < 7) {
                    $message = "{$product->name} dengan kadaluarsa " . Carbon::parse($stock->exp_date)->format('d-m-Y') . " tersisa {$stock->stock} stok lagi";
                    Notification::firstOrCreate(
                        ['message' => $message, 'notification_type' => 'Sisa Stok'],
                        ['notification_time' => now()]
                    );
                }

                // 2. Cek jika exp_date kurang dari 30 hari dari sekarang
                $expDate = Carbon::parse($stock->exp_date);
                $now = Carbon::now();
                $soon = $now->copy()->addDays(90);

                if ($expDate->greaterThanOrEqualTo($now) && $expDate->lessThanOrEqualTo($soon)) {
                    $message = "Pada {$product->name}, terdapat stok dengan tanggal expired " . $expDate->format('d-m-Y') . " yang sudah dekat";
                    Notification::firstOrCreate(
                        ['message' => $message, 'notification_type' => 'Tanggal Kadaluarsa'],
                        ['notification_time' => now()]
                    );
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }
    public function indexActiveProduct()
    {
        $products = Product::where('condition', 'active')
            ->with(['category', 'stocks'])
            ->withCount('transactionDetails')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }


    // public function index()
    // {
    //     try {
    //         $products = Product::with('category', 'stocks')
    //             ->withCount('transactionDetails')
    //             ->get()
    //             ->map(function ($product) {
    //                 return [
    //                     'id' => $product->id,
    //                     'name' => $product->name,
    //                     'category' => $product->category,
    //                     'price' => $product->price,
    //                     'photo' => $product->photo,
    //                     'stocks' => $product->stocks,
    //                     'is_in_transaction' => $product->transaction_details_count > 0 // Jika ada transaksi, TRUE
    //                 ];
    //             });

    //         return response()->json(['data' => $products], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    //     }
    // }


    // exp masing masing

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name'  => 'required',
    //         'price' => 'required|numeric',
    //         'stock' => 'required|numeric',
    //         'exp_date' => 'required|date',
    //         'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'description' => 'required',
    //         'category_id' => 'required'
    //     ]);

    //     $photoPath = null;
    //     if ($request->hasFile('photo')) {
    //         $photoPath = $request->file('photo')->store('product_photos', 'public');
    //     }

    //     $product = Product::create([
    //         'name'  => $request->name,
    //         'price' => $request->price,
    //         'stock' => $request->stock,
    //         'exp_date' => $request->exp_date,
    //         'photo' => $photoPath,
    //         'description' => $request->description,
    //         'category_id' => $request->category_id
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Produk berhasil ditambahkan!',
    //         'data' => $product
    //     ], 201);
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name'  => 'required',
    //         'price' => 'required|numeric',
    //         'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'description' => 'required',
    //         'category_id' => 'required',
    //         'stocks' => 'required|array', // Harus berbentuk array
    //         'stocks.*.exp_date' => 'required|date', // Setiap elemen harus punya tanggal expired
    //         'stocks.*.stock' => 'required|numeric|min:1' // Setiap elemen harus punya stok
    //     ]);

    //     // Simpan gambar jika ada
    //     $photoPath = $request->hasFile('photo')
    //         ? $request->file('photo')->store('product_photos', 'public')
    //         : null;

    //     // Simpan produk
    //     $product = Product::create([
    //         'name' => $request->name,
    //         'price' => $request->price,
    //         'photo' => $photoPath,
    //         'description' => $request->description,
    //         'category_id' => $request->category_id
    //     ]);

    //     // Simpan stok berdasarkan tanggal expired
    //     foreach ($request->stocks as $stockData) {
    //         ProductStock::create([
    //             'product_id' => $product->id,
    //             'exp_date' => $stockData['exp_date'],
    //             'stock' => $stockData['stock']
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Produk berhasil ditambahkan!',
    //         'data' => $product->load('stocks')
    //     ], 201);
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'code'  => 'required|unique:products,code',
    //         'name'  => 'required',
    //         'price' => 'required|numeric',
    //         'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'description' => 'required',
    //         'category_id' => 'required',
    //         'stocks' => 'required|array',
    //         'stocks.*.exp_date' => 'required|date',
    //         'stocks.*.stock' => 'required|numeric|min:1'
    //     ]);

    //     $photoPath = $request->hasFile('photo')
    //         ? $request->file('photo')->store('product_photos', 'public')
    //         : null;

    //     $product = Product::create([
    //         'code' => $request->code,
    //         'name' => $request->name,
    //         'price' => $request->price,
    //         'photo' => $photoPath,
    //         'description' => $request->description,
    //         'category_id' => $request->category_id
    //     ]);

    //     foreach ($request->stocks as $stockData) {
    //         ProductStock::create([
    //             'product_id' => $product->id,
    //             'exp_date' => $stockData['exp_date'],
    //             'stock' => $stockData['stock']
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Produk berhasil ditambahkan!',
    //         'data' => $product->load('stocks')
    //     ], 201);
    // }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'code' => 'required|unique:products,code',
                'name' => 'required',
                'price' => 'required|numeric',
                // 'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'description' => 'required',
                'category_id' => 'required',
                'stocks' => 'required|array',
                'stocks.*.exp_date' => 'required|date',
                'stocks.*.stock' => 'required|numeric|min:1'
            ]);

            // Cek apakah kode produk sudah ada
            if (Product::where('code', $request->code)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode produk sudah digunakan. Gunakan kode lain.'
                ], 422);
            }

            // Simpan foto jika ada
            $photoPath = $request->hasFile('photo')
                ? $request->file('photo')->store('product_photos', 'public')
                : null;

            // Simpan produk ke database
            $product = Product::create([
                'code' => $request->code,
                'name' => $request->name,
                'price' => $request->price,
                'photo' => $photoPath,
                'description' => $request->description,
                'category_id' => $request->category_id
            ]);

            // Simpan stok produk
            foreach ($request->stocks as $stockData) {
                ProductStock::create([
                    'product_id' => $product->id,
                    'exp_date' => $stockData['exp_date'],
                    'stock' => $stockData['stock']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan!',
                'data' => $product->load('stocks')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi
            return response()->json([
                'success' => false,
                'message' => 'Kode produk sudah pernah digunakan!',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Tangani error lainnya
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // exp masing masing

    // public function show($id)
    // {
    //     // $product = Product::findOrFail($id);
    //     $product = Product::with('category')->find($id);

    //     if (!$product) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Produk tidak ditemukan'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $product
    //     ], 200);
    // }

    public function show($id)
    {
        $product = Product::with(['category', 'stocks'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ], 200);
    }


    public function showByCategory($category_id)
    {
        $products = Product::with('category', 'stocks')->where('category_id', $category_id)->get();

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk untuk kategori ini'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }

    public function showByCategoryActiveProduct($category_id)
    {
        $products = Product::with('category', 'stocks')
            ->where('category_id', $category_id)
            ->where('condition', 'active')
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk untuk kategori ini'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }

    // exp masing masing

    // public function update(Request $request, $id)
    // {
    //     $product = Product::find($id);

    //     if (!$product) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Produk tidak ditemukan'
    //         ], 404);
    //     }

    //     $request->validate([
    //         'name'  => 'required',
    //         'price' => 'required|numeric',
    //         'stock' => 'required|numeric',
    //         'exp_date' => 'required|date',
    //         'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'description' => 'required',
    //         'category_id' => 'required',
    //     ]);

    //     if ($request->hasFile('photo')) {
    //         if ($product->photo) {
    //             Storage::disk('public')->delete($product->photo);
    //         }
    //         $photoPath = $request->file('photo')->store('product_photos', 'public');
    //         $product->photo = $photoPath;
    //     }

    //     if ($request->_method === 'PUT') {
    //         $request->request->remove('_method');
    //     }

    //     $product->update([
    //         'name'  => $request->name,
    //         'price' => $request->price,
    //         'stock' => $request->stock,
    //         'exp_date' => $request->exp_date,
    //         'photo' => $product->photo ?? null,
    //         'description' => $request->description,
    //         'category_id' => $request->category_id
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Produk berhasil diperbarui!',
    //         'data' => $product
    //     ], 200);
    // }

    // public function update(Request $request, $id)
    // {
    //     $product = Product::find($id);

    //     if (!$product) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Produk tidak ditemukan'
    //         ], 404);
    //     }

    //     $request->validate([
    //         'name'  => 'required',
    //         'price' => 'required|numeric',
    //         'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'description' => 'required',
    //         'category_id' => 'required',
    //         'stocks' => 'required|array',
    //         'stocks.*.exp_date' => 'required|date',
    //         'stocks.*.stock' => 'required|numeric|min:1'
    //     ]);

    //     // Update data produk
    //     $product->update([
    //         'name' => $request->name,
    //         'price' => $request->price,
    //         'description' => $request->description,
    //         'category_id' => $request->category_id,
    //         'photo' => $request->hasFile('photo') ? $request->file('photo')->store('product_photos', 'public') : $product->photo,
    //     ]);

    //     // Hapus stok lama dan simpan yang baru
    //     $product->stocks()->delete();
    //     foreach ($request->stocks as $stockData) {
    //         ProductStock::create([
    //             'product_id' => $product->id,
    //             'exp_date' => $stockData['exp_date'],
    //             'stock' => $stockData['stock']
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Produk berhasil diperbarui!',
    //         'data' => $product->load('stocks')
    //     ], 200);
    // }

    // public function update(Request $request, $id)
    // {
    //     $product = Product::find($id);

    //     if (!$product) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Produk tidak ditemukan'
    //         ], 404);
    //     }

    //     $request->validate([
    //         'code'  => 'required|unique:products,code,' . $id,
    //         'name'  => 'required',
    //         'price' => 'required|numeric',
    //         'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'description' => 'required',
    //         'category_id' => 'required',
    //         'stocks' => 'required|array',
    //         'stocks.*.exp_date' => 'required|date',
    //         'stocks.*.stock' => 'required|numeric|min:1'
    //     ]);

    //     $product->update([
    //         'code' => $request->code,
    //         'name' => $request->name,
    //         'price' => $request->price,
    //         'description' => $request->description,
    //         'category_id' => $request->category_id,
    //         'photo' => $request->hasFile('photo') ? $request->file('photo')->store('product_photos', 'public') : $product->photo,
    //     ]);

    //     $product->stocks()->delete();
    //     foreach ($request->stocks as $stockData) {
    //         ProductStock::create([
    //             'product_id' => $product->id,
    //             'exp_date' => $stockData['exp_date'],
    //             'stock' => $stockData['stock']
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Produk berhasil diperbarui!',
    //         'data' => $product->load('stocks')
    //     ], 200);
    // }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $product = Product::find($id);

    //         if (!$product) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Produk tidak ditemukan'
    //             ], 404);
    //         }

    //         $request->validate([
    //             'code' => [
    //                 'required',
    //                 Rule::unique('products', 'code')->ignore($id),
    //             ],
    //             'name' => 'required',
    //             'price' => 'required|numeric',
    //             'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //             'description' => 'required',
    //             'category_id' => 'required',
    //             'stocks' => 'required|array',
    //             'stocks.*.exp_date' => 'required|date',
    //             'stocks.*.stock' => 'required|numeric|min:1'
    //         ], [
    //             'code.unique' => 'Kode produk sudah digunakan, silakan gunakan kode lain.'
    //         ]);

    //         $product->update([
    //             'code' => $request->code,
    //             'name' => $request->name,
    //             'price' => $request->price,
    //             'description' => $request->description,
    //             'category_id' => $request->category_id,
    //             'photo' => $request->hasFile('photo') ? $request->file('photo')->store('product_photos', 'public') : $product->photo,
    //         ]);

    //         $product->stocks()->delete();
    //         foreach ($request->stocks as $stockData) {
    //             ProductStock::create([
    //                 'product_id' => $product->id,
    //                 'exp_date' => $stockData['exp_date'],
    //                 'stock' => $stockData['stock']
    //             ]);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Produk berhasil diperbarui!',
    //             'data' => $product->load('stocks')
    //         ], 200);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         // Tangani error validasi
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terdapat Field yang belum diisi',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         // Tangani error lainnya
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan saat menyimpan produk',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function update(Request $request, $id)
    {
        try {
            Log::info("Memulai proses update produk dengan ID: $id");

            $product = Product::find($id);

            if (!$product) {
                Log::warning("Produk dengan ID $id tidak ditemukan.");
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            Log::info("Produk ditemukan. Memvalidasi input.");

            $request->validate([
                'code' => [
                    'required',
                    Rule::unique('products', 'code')->ignore($id),
                ],
                'name' => 'required',
                'price' => 'required|numeric',
                // 'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'description' => 'required',
                'category_id' => 'required',
                'stocks' => 'required|array',
                'stocks.*.exp_date' => 'required|date',
                'stocks.*.stock' => 'required|numeric|min:1'
            ], [
                'code.unique' => 'Kode produk sudah digunakan, silakan gunakan kode lain.'
            ]);

            Log::info("Validasi berhasil. Memperbarui produk.");

            $product->update([
                'code' => $request->code,
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'photo' => $request->hasFile('photo')
                    ? $request->file('photo')->store('product_photos', 'public')
                    : $product->photo,
            ]);

            Log::info("Produk berhasil diperbarui. Menghapus stok lama.");

            $product->stocks()->delete();

            Log::info("Menambahkan stok baru.");
            foreach ($request->stocks as $index => $stockData) {
                Log::info("Menambahkan stok ke-$index: ", $stockData);
                ProductStock::create([
                    'product_id' => $product->id,
                    'exp_date' => $stockData['exp_date'],
                    'stock' => $stockData['stock']
                ]);
            }

            Log::info("Semua stok berhasil ditambahkan untuk produk ID $product->id.");

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui!',
                'data' => $product->load('stocks')
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validasi gagal: ", $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Terdapat Field yang belum diisi',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Kesalahan saat menyimpan produk: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function destroy($id)
    // {
    //     $product = Product::find($id);

    //     if (!$product) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Produk tidak ditemukan'
    //         ], 404);
    //     }

    //     $product->delete();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Produk berhasil dihapus!'
    //     ], 200);
    // }

    // public function updateCondition($id)
    // {
    //     $product = Product::find($id);

    //     if (!$product) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Produk tidak ditemukan'
    //         ], 404);
    //     }

    //     $product->update(['condition' => 'nonactive']);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Produk berhasil dinonaktifkan!'
    //     ], 200);
    // }

    // public function updateCondition(Request $request, $id)
    // {
    //     $product = Product::find($id);

    //     if (!$product) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Produk tidak ditemukan'
    //         ], 404);
    //     }

    //     $newCondition = $request->input('condition'); // Ambil status baru dari request

    //     if (!in_array($newCondition, ['active', 'nonactive'])) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Status tidak valid'
    //         ], 400);
    //     }

    //     $product->update(['condition' => $newCondition]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Status produk diperbarui menjadi ' . $newCondition,
    //         'condition' => $newCondition
    //     ], 200);
    // }

    // public function updateCondition(Request $request, $id)
    // {
    //     $product = Product::find($id);

    //     if (!$product) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Produk tidak ditemukan'
    //         ], 404);
    //     }

    //     $newCondition = $request->input('condition');

    //     if (!in_array($newCondition, ['active', 'nonactive'])) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Status tidak valid'
    //         ], 400);
    //     }

    //     // Update condition
    //     $product->update(['condition' => $newCondition]);

    //     // Catat riwayat jika status menjadi nonaktif
    //     if ($newCondition === 'nonactive') {
    //         \App\Models\ProductStatusHistory::create([
    //             'product_id' => $product->id,
    //             'condition' => 'nonactive',
    //             'changed_at' => now()
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Status produk diperbarui menjadi ' . $newCondition,
    //         'condition' => $newCondition
    //     ], 200);
    // }

    // public function updateCondition(Request $request, $id)
    // {
    //     $product = Product::with('stocks')->find($id);

    //     if (!$product) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Produk tidak ditemukan'
    //         ], 404);
    //     }

    //     $newCondition = $request->input('condition');

    //     if (!in_array($newCondition, ['active', 'nonactive'])) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Status tidak valid'
    //         ], 400);
    //     }

    //     $product->update(['condition' => $newCondition]);

    //     if ($newCondition === 'nonactive') {
    //         // // Ambil ringkasan stok (stock dan exp_date)
    //         // $stockSummary = $product->stocks->map(function ($stock) {
    //         //     return [
    //         //         'stock' => $stock->stock,
    //         //         'exp_date' => $stock->exp_date,
    //         //     ];
    //         // });

    //         // ProductStatusHistory::create([
    //         //     'product_id' => $product->id,
    //         //     'condition' => 'nonactive',
    //         //     'changed_at' => now(),
    //         //     'stock_summary' => $stockSummary,
    //         // ]);

    //         // Ambil stok exp paling awal (terdekat)
    //         $latestStock = $product->stocks()
    //             ->orderBy('exp_date', 'asc')
    //             ->first();

    //         ProductStatusHistory::create([
    //             'product_id' => $product->id,
    //             'condition' => 'nonactive',
    //             'changed_at' => now(),
    //             'product_exp_date' => $latestStock?->exp_date,
    //             'product_stock' => $latestStock?->stock,
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Status produk diperbarui menjadi ' . $newCondition,
    //         'condition' => $newCondition
    //     ], 200);
    // }

    public function updateCondition(Request $request, $id)
    {
        $product = Product::with('stocks')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $newCondition = $request->input('condition');

        if (!in_array($newCondition, ['active', 'nonactive'])) {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak valid'
            ], 400);
        }

        $product->update(['condition' => $newCondition]);

        if ($newCondition === 'nonactive') {
            // Simpan riwayat status
            $history = ProductStatusHistory::create([
                'product_id' => $product->id,
                'condition' => 'nonactive',
                'changed_at' => now(),
            ]);

            // Simpan exp_date dan stok dari setiap baris di tabel product_stocks
            foreach ($product->stocks as $stock) {
                ProductStatusHistoryDetail::create([
                    'product_status_history_id' => $history->id,
                    'product_exp_date' => $stock->exp_date,
                    'product_stock' => $stock->stock
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status produk diperbarui menjadi ' . $newCondition,
            'condition' => $newCondition
        ], 200);
    }

    // public function getNonactiveHistory()
    // {
    //     // Ambil semua histori produk yang pernah di-nonaktifkan, beserta relasi produk
    //     $histories = \App\Models\ProductStatusHistory::with('product')
    //         ->where('condition', 'nonactive')
    //         ->orderBy('changed_at', 'desc')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Daftar produk yang pernah dinonaktifkan',
    //         'data' => $histories
    //     ]);
    // }

    // public function getNonactiveProducts()
    // {
    //     // $products = ProductStatusHistory::with(['product'])
    //     //     // ->where('condition', 'nonactive')
    //     //     ->orderByDesc('changed_at') // Diasumsikan produk di-nonaktifkan saat update
    //     //     ->get()

    //     $products = ProductStatusHistory::with(['product.category', 'product.stocks'])
    //         ->where('condition', 'nonactive') // pastikan nilai sesuai dengan DB Anda
    //         ->orderByDesc('changed_at')
    //         ->get()
    //         // ->map(function ($product) {
    //         //     return [
    //         //         'id' => $product->id,
    //         //         'code' => $product->code,
    //         //         'name' => $product->name,
    //         //         'photo' => $product->photo,
    //         //         'price' => $product->price,
    //         //         'category' => $product->category,
    //         //         'stocks' => $product->stocks,
    //         //         'condition' => $product->condition,
    //         //         'nonactive_at' => $product->changed_at->format('Y-m-d H:i:s'),
    //         //     ];
    //         // });

    //         ->map(function ($history) {
    //             if (!$history->product) {
    //                 return null;
    //             }

    //             return [
    //                 'id' => $history->product_id,
    //                 'code' => $history->product->code,
    //                 'name' => $history->product->name,
    //                 'photo' => $history->product->photo,
    //                 'price' => $history->product->price,
    //                 'category' => optional($history->product->category)->name, // pastikan ini nullable
    //                 'stocks' => $history->product->stocks,
    //                 'condition' => $history->condition,
    //                 'nonactive_at' => $history->changed_at->format('Y-m-d H:i:s'),
    //             ];
    //         })->filter()->values(); // filter() untuk hilangkan null, values() untuk reindex


    //     return response()->json(['data' => $products]);
    // }

    // public function getNonactiveProducts()
    // {
    //     // Ambil semua histori status produk terakhir (dengan asumsi produk bisa punya lebih dari 1 histori)
    //     $latestStatuses = ProductStatusHistory::with(['product.category', 'product.stocks'])
    //         ->orderByDesc('changed_at')
    //         ->get()
    //         ->groupBy('product_id') // Kelompokkan per produk
    //         ->map(function ($statusGroup) {
    //             // Ambil histori status terbaru untuk setiap produk
    //             return $statusGroup->first();
    //         })
    //         ->filter(function ($history) {
    //             // Filter hanya yang statusnya 'nonactive' dan produk-nya masih ada
    //             return $history->condition === 'nonactive' && $history->product;
    //         })
    //         ->map(function ($history) {
    //             return [
    //                 'id' => $history->product->id,
    //                 'code' => $history->product->code,
    //                 'name' => $history->product->name,
    //                 'photo' => $history->product->photo,
    //                 'price' => $history->product->price,
    //                 'category' => optional($history->product->category)->name,
    //                 'stocks' => $history->product->stocks, // Bisa diubah jadi sum, count, atau lainnya sesuai kebutuhan
    //                 'condition' => $history->condition,
    //                 'nonactive_at' => $history->changed_at->format('Y-m-d H:i:s'),
    //             ];
    //         })
    //         ->values(); // reset index agar rapih

    //     return response()->json(['success' => true, 'data' => $latestStatuses]);
    // }

    // public function showProductBasedOnCategory($categoryId)
    // {
    //     $products = Product::where('category_id', $categoryId)->get()->map(function ($product) {
    //         $product->image_url = url('storage/' . $product->image_url_);
    //         return $product;
    //     });

    //     if ($products->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Tidak ada produk untuk kategori ini'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $products
    //     ], 200);
    // }

    // public function canDelete($id)
    // {
    //     $productInTransaction = \App\Models\TransactionDetail::where('product_id', $id)->exists();

    //     return response()->json([
    //         'can_delete' => !$productInTransaction
    //     ]);
    // }

    // public function checkTransaction($id)
    // {
    //     $exists = DB::table('transactions')->where('product_id', $id)->exists();

    //     return response()->json(['exists' => $exists]);
    // }

    public function getExpDates($product_id)
    {
        $exp_dates = ProductStock::where('product_id', $product_id)
            ->where('stock', '>', 0)
            ->orderBy('exp_date')
            ->pluck('exp_date');

        return response()->json([
            'success' => true,
            'data' => $exp_dates
        ]);
    }

    // public function getTotalStock($product_id)
    // {
    //     $totalStock = ProductStock::where('product_id', $product_id)->sum('stock');

    //     return response()->json(['stock' => $totalStock]);
    // }

    // public function getStockByDate($product_id, $exp_date)
    // {
    //     $stock = ProductStock::where('product_id', $product_id)
    //         ->where('exp_date', $exp_date)
    //         ->value('stock');

    //     return response()->json(['stock' => $stock ?? 0]);
    // }

    // public function getTotalStock($product_id)
    // {
    //     $totalStock = ProductStock::where('product_id', $product_id)->sum('stock');

    //     return response()->json(['stock' => $totalStock ?? 0]);
    // }

    // public function getStockByDate($product_id, $exp_date)
    // {
    //     $stock = ProductStock::where('product_id', $product_id)
    //         ->where('exp_date', $exp_date)
    //         ->first();

    //     return response()->json(['stock' => $stock ? $stock->stock : 0]);
    // }

    // private function deleteEmptyStocks()
    // {
    //     ProductStock::where('stock', 0)->delete();
    // }

    public function productStocksReport()
    {
        $entryStocks = DB::table('entry_products')
            ->join('products', 'entry_products.product_id', '=', 'products.id')
            ->select(
                'entry_products.id',
                'products.name',
                'products.code',
                'products.photo',
                'products.price',
                'entry_products.exp_date',
                'entry_products.previous_stock',
                'entry_products.added_stock as quantity',
                'entry_products.current_stock',
                'entry_products.created_at as timestamp'
            )
            ->get();

        $exitStocks = DB::table('exit_products')
            ->join('products', 'exit_products.product_id', '=', 'products.id')
            ->select(
                'exit_products.id',
                'products.name',
                'products.code',
                'products.photo',
                'products.price',
                'exit_products.exp_date',
                'exit_products.previous_stock',
                'exit_products.removed_stock as quantity',
                'exit_products.current_stock',
                'exit_products.created_at as timestamp'
            )
            ->get();

        $reportData = $entryStocks->merge($exitStocks)->sortByDesc('timestamp')->values()->all();

        return response()->json([
            'success' => true,
            'data' => $reportData
        ]);
    }

    public function getNonactiveProducts()
    {
        // Ambil semua riwayat produk yang dinonaktifkan
        $nonactiveHistories = ProductStatusHistory::with('product.category', 'details')
            ->where('condition', 'nonactive')
            ->orderBy('changed_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar riwayat produk yang dinonaktifkan',
            'data' => $nonactiveHistories
        ]);
    }

    // public function getNonactiveProducts()
    // {
    //     Log::info('Memulai pengambilan riwayat produk yang dinonaktifkan');

    //     try {
    //         // Ambil semua riwayat produk yang dinonaktifkan
    //         $nonactiveHistories = ProductStatusHistory::with('product')
    //             ->where('condition', 'nonactive')
    //             ->orderBy('changed_at', 'desc')
    //             ->get();

    //         Log::info('Berhasil mengambil riwayat produk nonaktif', [
    //             'jumlah_data' => $nonactiveHistories->count()
    //         ]);

    //         return response()->json([
    //             'message' => 'Daftar riwayat produk yang dinonaktifkan',
    //             'data' => $nonactiveHistories
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Gagal mengambil riwayat produk nonaktif', [
    //             'error' => $e->getMessage()
    //         ]);

    //         return response()->json([
    //             'message' => 'Terjadi kesalahan saat mengambil data',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function toggleFavorite(Request $request, $productId)
    {
        $user = Auth::user();  // Get the currently authenticated user

        // Check if the product is already in the favorites table
        $favorite = Favorite::where('product_id', $productId)
            ->where('user_id', $user->id)
            ->first();

        if ($favorite) {
            // If it's already a favorite, remove it
            $favorite->delete();
            return response()->json(['message' => 'Product removed from favorites'], 200);
        } else {
            // Otherwise, add the product to favorites
            Favorite::create([
                'product_id' => $productId,
                'user_id' => $user->id,
            ]);
            return response()->json(['message' => 'Product added to favorites'], 200);
        }
    }

    public function checkFavorite($productId)
    {
        $user = Auth::user();

        $isFavorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'isFavorite' => $isFavorite
        ]);
    }

    // public function getFavorites(Request $request)
    // {
    //     $user = $request->user();

    //     // Ambil produk favorit user berdasarkan relasi
    //     $favorites = $user->favorites()->with('product')->get();

    //     return response()->json([
    //         'status' => true,
    //         'favorites' => $favorites->map(function ($favorite) {
    //             return [
    //                 'id' => $favorite->product->id,
    //                 'name' => $favorite->product->name,
    //                 'description' => $favorite->product->description,
    //                 // 'stock' => $favorite->product->stock,
    //                 'price' => $favorite->product->price,
    //                 'photo' => $favorite->product->photo,
    //             ];
    //         }),
    //     ]);
    // }

    // public function getFavorites(Request $request)
    // {
    //     // $user = auth()->user();
    //     $user = Auth::user();

    //     $favorites = $user->favorites->with([
    //         'category:id,name', // relasi ke kategori
    //         'stocks:id,product_id,exp_date,stock' // relasi ke stok
    //     ])->get();

    //     return response()->json([
    //         'favorites' => $favorites
    //     ]);
    // }

    public function getFavorites(Request $request)
    {
        $user = Auth::user();

        $favorites = $user->favorites()->with([
            'category:id,name',
            'stocks:id,product_id,exp_date,stock'
        ])->get();

        return response()->json([
            'favorites' => $favorites
        ]);
    }

    // public function getAllFavorites()
    // {
    //     $user = Auth::user();

    //     $favoriteProductIds = Favorite::where('user_id', $user->id)
    //         ->pluck('product_id');

    //     return response()->json([
    //         'favoriteProductIds' => $favoriteProductIds
    //     ]);
    // }

    public function getAllFavorites()
    {
        $user = Auth::user();

        $favoriteProducts = Favorite::where('user_id', $user->id)
            ->with('product') // pastikan relasi favorite->product ada
            ->get()
            ->pluck('product');

        return response()->json([
            'favorites' => $favoriteProducts
        ]);
    }
}

