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
    public function index()
    {
        $products = Product::with(['category', 'stocks'])->withCount('transactionDetails')->get();

        foreach ($products as $product) {
            foreach ($product->stocks as $stock) {
                if ($stock->stock < 7) {
                    $message = "{$product->name} dengan kadaluarsa " . Carbon::parse($stock->exp_date)->format('d-m-Y') . " tersisa {$stock->stock} stok lagi";
                    Notification::firstOrCreate(
                        ['message' => $message, 'notification_type' => 'Sisa Stok'],
                        ['notification_time' => now()]
                    );
                }

                $expDate = Carbon::parse($stock->exp_date);
                $now = Carbon::now();
                $soon = $now->copy()->addDays(90);

                if ($expDate->greaterThanOrEqualTo($now) && $expDate->lessThanOrEqualTo($soon)) {
                    $message = "Pada {$product->name}, terdapat stok dengan tanggal expired " . $expDate->format('d-m-Y') . ' yang sudah dekat';
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

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code' => 'required|unique:products,code',
                'name' => 'required',
                'price' => 'required|numeric',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'description' => 'required',
                'category_id' => 'required',
                'stocks' => 'required|array',
                'stocks.*.exp_date' => 'required|date',
                'stocks.*.stock' => 'required|numeric|min:0'
            ]);

            if (Product::where('code', $request->code)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode produk sudah digunakan. Gunakan kode lain.'
                ], 422);
            }

            $photoPath = $request->hasFile('photo')
                ? $request->file('photo')->store('product_photos', 'public')
                : null;

            $product = Product::create([
                'code' => $request->code,
                'name' => $request->name,
                'price' => $request->price,
                'photo' => $photoPath,
                'description' => $request->description,
                'category_id' => $request->category_id
            ]);

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
            return response()->json([
                'success' => false,
                'message' => 'Kode produk sudah pernah digunakan!',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validatedData = $request->validate([
                'code' => 'required|unique:products,code,' . $id,
                'name' => 'required',
                'price' => 'required|numeric',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'description' => 'required',
                'category_id' => 'required',
                'stocks' => 'nullable|array',
                'stocks.*.exp_date' => 'nullable|date',
                'stocks.*.stock' => 'nullable|numeric|min:0'
            ]);

            if ($request->hasFile('photo')) {
                if ($product->photo && \Storage::disk('public')->exists($product->photo)) {
                    \Storage::disk('public')->delete($product->photo);
                }

                $photoPath = $request->file('photo')->store('product_photos', 'public');
            } else {
                $photoPath = $product->photo;
            }

            $product->update([
                'code' => $request->code,
                'name' => $request->name,
                'price' => $request->price,
                'photo' => $photoPath,
                'description' => $request->description,
                'category_id' => $request->category_id
            ]);

            $product->stocks()->delete();

            foreach ($request->stocks as $stockData) {
                ProductStock::create([
                    'product_id' => $product->id,
                    'exp_date' => $stockData['exp_date'],
                    'stock' => $stockData['stock']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui!',
                'data' => $product->load('stocks')
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal!',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

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
            $history = ProductStatusHistory::create([
                'product_id' => $product->id,
                'condition' => 'nonactive',
                'changed_at' => now(),
            ]);

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

    public function getExpDates($product_id)
    {
        $exp_dates = ProductStock::where('product_id', $product_id)
            ->where('stock', '>=', 0)
            ->orderBy('exp_date')
            ->pluck('exp_date');

        return response()->json([
            'success' => true,
            'data' => $exp_dates
        ]);
    }

    public function getTotalStock($product_id)
    {
        $totalStock = ProductStock::where('product_id', $product_id)->sum('stock');

        return response()->json(['stock' => $totalStock ?? 0]);
    }

    public function getStockByDate($product_id, $exp_date)
    {
        $stock = ProductStock::where('product_id', $product_id)
            ->where('exp_date', $exp_date)
            ->first();

        return response()->json(['stock' => $stock ? $stock->stock : 0]);
    }

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
        $nonactiveHistories = ProductStatusHistory::with('product.category', 'details')
            ->where('condition', 'nonactive')
            ->orderBy('changed_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar riwayat produk yang dinonaktifkan',
            'data' => $nonactiveHistories
        ]);
    }

    public function toggleFavorite(Request $request, $productId)
    {
        $user = Auth::user();

        $favorite = Favorite::where('product_id', $productId)
            ->where('user_id', $user->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Product removed from favorites'], 200);
        } else {
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

    public function getAllFavorites()
    {
        $user = Auth::user();

        $favoriteProducts = Favorite::where('user_id', $user->id)
            ->with(['product.stocks', 'product.category'])
            ->get()
            ->pluck('product');

        return response()->json([
            'favorites' => $favoriteProducts
        ]);
    }

    /**
     * Menambahkan tanggal expired baru untuk sebuah produk.
     * Stok awal akan di-set menjadi 0.
     */
    public function addExpireDate(Request $request, Product $product)
    {
        $request->validate([
            'exp_date' => 'required|date|after_or_equal:today',
        ]);

        $existingStock = ProductStock::where('product_id', $product->id)
            ->where('exp_date', $request->exp_date)
            ->first();

        if ($existingStock) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal expired ini sudah terdaftar untuk produk tersebut.',
            ], 409);
        }

        ProductStock::create([
            'product_id' => $product->id,
            'exp_date' => $request->exp_date,
            'stock' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tanggal expired baru berhasil ditambahkan.',
        ]);
    }

    /**
     * Menghapus record tanggal expired (ProductStock) untuk sebuah produk.
     * Hanya bisa dilakukan jika stok untuk tanggal tersebut adalah 0.
     */
    public function destroyExpireDate(Request $request, Product $product, $exp_date)
    {
        $productStock = ProductStock::where('product_id', $product->id)
            ->where('exp_date', $exp_date)
            ->first();

        if (!$productStock) {
            return response()->json([
                'success' => false,
                'message' => 'Data tanggal expired tidak ditemukan.',
            ], 404);
        }

        if ($productStock->stock > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus. Stok untuk tanggal ini masih tersedia.',
            ], 422);
        }

        $productStock->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tanggal expired berhasil dihapus.',
        ]);
    }
}
