<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartDetail;
use App\Models\Transaction;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TransactionStatusHistory;

class CartController extends Controller
{
    // public function addToCart(Request $request)
    // {
    //     $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'shipping_method' => 'nullable|in:Reguler,Express,Ambil di tempat',
    //         'payment_method' => 'nullable|in:Cash,Bank Transfer,OVO,Dana,COD',
    //         'quantities' => 'required|array',
    //         'quantities.*' => 'integer|min:1'
    //     ]);

    //     $userId = Auth::id();
    //     $product = Product::find($request->product_id);

    //     // Ambil shipping dan payment method dari item cart pertama user (jika ada)
    //     $existingCartItem = Cart::where('user_id', $userId)->first();

    //     $shippingMethod = $request->shipping_method
    //         ?? ($existingCartItem ? $existingCartItem->shipping_method : 'Ambil di tempat');

    //     $paymentMethod = $request->payment_method
    //         ?? ($existingCartItem ? $existingCartItem->payment_method : 'Cash');

    //     foreach ($request->quantities as $exp_date => $quantity) {
    //         $existingCart = Cart::where('user_id', $userId)
    //             ->where('product_id', $request->product_id)
    //             ->where('exp_date', $exp_date)
    //             ->first();

    //         $baseTotal = $product->price * $quantity;
    //         $totalWithShipping = $baseTotal;

    //         if ($existingCart) {
    //             $existingCart->quantity += $quantity;
    //             $existingCart->gross_amount += $totalWithShipping;
    //             $existingCart->shipping_method = $shippingMethod;
    //             $existingCart->payment_method = $paymentMethod;
    //             $existingCart->save();
    //         } else {
    //             Cart::create([
    //                 'user_id' => $userId,
    //                 'product_id' => $request->product_id,
    //                 'exp_date' => $exp_date,
    //                 'quantity' => $quantity,
    //                 'gross_amount' => $totalWithShipping,
    //                 'shipping_method' => $shippingMethod,
    //                 'payment_method' => $paymentMethod,
    //             ]);
    //         }
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Produk berhasil ditambahkan ke keranjang'
    //     ], 200);
    // }

    // Fungsi untuk menambahkan data produk ke keranjang
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'shipping_method' => 'nullable|in:Reguler,Express,Ambil di tempat',
            'payment_method' => 'nullable|in:Cash,Bank Transfer,OVO,Dana,COD',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1'
        ]);

        $userId = Auth::id();
        $product = Product::find($request->product_id);

        // Ambil shipping dan payment method dari item cart pertama user (jika ada)
        $existingCartItem = Cart::where('user_id', $userId)->first();
        $shippingMethod = $request->shipping_method ?? ($existingCartItem ? $existingCartItem->shipping_method : 'Ambil di tempat');
        $paymentMethod = $request->payment_method ?? ($existingCartItem ? $existingCartItem->payment_method : 'Cash');

        // Ambil semua exp_date dari product_stocks untuk produk ini
        $productStocks = ProductStock::where('product_id', $request->product_id)->get();

        foreach ($productStocks as $stock) {
            $exp_date = $stock->exp_date;
            $quantity = $request->quantities[$exp_date] ?? 0; // Ambil quantity dari request atau 0 jika tidak ada

            if ($quantity >= 0) { // Hanya tambahkan jika quantity lebih dari 0
                $existingCart = Cart::where('user_id', $userId)
                    ->where('product_id', $request->product_id)
                    ->where('exp_date', $exp_date)
                    ->first();

                $baseTotal = $product->price * $quantity;
                $totalWithShipping = $baseTotal;

                if ($existingCart) {
                    $existingCart->quantity += $quantity;
                    $existingCart->gross_amount += $totalWithShipping;
                    $existingCart->shipping_method = $shippingMethod;
                    $existingCart->payment_method = $paymentMethod;
                    $existingCart->save();
                } else {
                    Cart::create([
                        'user_id' => $userId,
                        'product_id' => $request->product_id,
                        'exp_date' => $exp_date,
                        'quantity' => $quantity,
                        'gross_amount' => $totalWithShipping,
                        'shipping_method' => $shippingMethod,
                        'payment_method' => $paymentMethod,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang'
        ], 200);
    }

    // public function updateCart(Request $request, $id)
    // {
    //     $request->validate([
    //         'quantity' => 'required|integer|min:0',
    //     ]);

    //     $cart = Cart::findOrFail($id);

    //     if ($cart->user_id !== Auth::id()) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $cart->quantity = $request->quantity;
    //     $cart->gross_amount = ($cart->product->price * $request->quantity);
    //     $cart->save();

    //     return response()->json(['success' => true, 'cart' => $cart], 200);
    // }

    // Fungsi untuk update data produk di dalam keranjang 
    public function updateCart(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = Cart::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        // if ($cart->user_id !== Auth::id()) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        $cart->quantity = $request->quantity;
        $cart->gross_amount = ($cart->product->price * $request->quantity);
        $cart->save();

        return response()->json(['success' => true, 'cart' => $cart], 200);
    }

    // public function deleteCart($id)
    // {
    //     $cart = Cart::findOrFail($id);

    //     if ($cart->user_id !== Auth::id()) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $cart->delete();
    //     return response()->json(['success' => true, 'message' => 'Item removed from cart'], 200);
    // }

    // Fungsi untuk menghapus data produk dari keranjang
    public function deleteCart($productId)
    {
        $userId = Auth::id();

        // Ambil semua entri cart milik user untuk produk tertentu
        $cartItems = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Item tidak ditemukan'], 404);
        }

        // Hapus semua entri
        foreach ($cartItems as $cart) {
            $cart->delete();
        }

        return response()->json(['success' => true, 'message' => 'Item berhasil dihapus berdasarkan product_id'], 200);
    }

    // Fungsi untuk melakukan checkout pada biaya produk dalam keranjang
    public function checkout()
    {
        DB::beginTransaction();
        try {
            // $cartItems = Cart::all();

            $userId = Auth::id();
            $cartItems = Cart::where('user_id', $userId)->get();

            // $userId = auth()->id();

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Keranjang kosong'], 400);
            }

            // Ambil metode dari item pertama dalam cart
            $firstItem = $cartItems->first();
            $shippingMethod = $firstItem->shipping_method;
            $paymentMethod = $firstItem->payment_method;

            $shippingCost = match ($shippingMethod) {
                'Express' => 3500,
                'Reguler' => 1750,
                default => 0,
            };

            $gross_amount = $cartItems->sum('gross_amount');
            $total_payment = $gross_amount; // kalau belum ada ongkir
            $transactionCode = 'TRX' . strtoupper(uniqid());

            // Tentukan status berdasarkan shipping method
            $initialStatus = ($shippingMethod === 'Ambil di tempat') ? 'Pesanan selesai' : 'Dalam pengemasan';
            $is_final = ($shippingMethod === 'Ambil di tempat') ? 'Sudah selesai' : 'Belum selesai';

            $transaction = Transaction::create([
                'transaction_code' => $transactionCode,
                'gross_amount' => $gross_amount,
                'total_payment' => $total_payment + $shippingCost,
                'payment_method' => $paymentMethod,
                'shipping_method' => $shippingMethod,
                'shipping_cost' => $shippingCost,
                // 'status' => 'Dalam pengemasan', // status awal default
                'status' => $initialStatus, // status awal default
                'user_id' => $userId,
                'transaction_date' => now(),
                'is_final' => $is_final
            ]);

            TransactionStatusHistory::create([
                'transaction_id' => $transaction->id,
                // 'status' => 'Dalam pengemasan',
                'status' => $initialStatus,
                'changed_at' => now(),
            ]);

            foreach ($cartItems as $item) {
                $stock = ProductStock::where('product_id', $item->product_id)
                    ->where('exp_date', $item->exp_date)
                    ->where('stock', '>=', $item->quantity)
                    ->first();

                if (!$stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi untuk produk ID {$item->product_id} dengan tanggal expired {$item->exp_date}"
                    ], 400);
                }

                $product = Product::find($item->product_id); // pastikan model Product ada

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'exp_date' => $item->exp_date,
                    'quantity' => $item->quantity,
                    'product_name' => $product->name,
                    'product_code' => $product->code,
                    'product_price' => $product->price,
                    'product_photo' => $product->photo,
                    'stock_before' => $stock->stock + $item->quantity,
                    'stock_after' => $stock->stock,
                ]);

                $stock->stock -= $item->quantity;
                $stock->stock == 0 ? $stock->delete() : $stock->save();

                $item->delete();
            }

            DB::commit();
            return response()->json(['success' => true, 'transaction' => $transaction], 201);
        }
      
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal melakukan transaksi',
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }

    // Fungsi untuk menampilkan data keranjang
    public function show()
    {
        $userId = Auth::id(); 

        // Ambil semua item cart milik user saat ini
        $cartItems = Cart::with('product')
            ->where('user_id', $userId)
            ->get()
            ->groupBy(function ($item) {
                return $item->product_id . '_' . $item->exp_date;
            });

        // Daftar product_id unik dari cart user
        $productIdsInCart = $cartItems->pluck('0.product_id')->unique()->values();

        // Ambil seluruh expired date + stok untuk masing-masing product_id
        $productStockList = ProductStock::whereIn('product_id', $productIdsInCart)->get()->groupBy('product_id');

        $shippingMethods = ['Reguler', 'Express', 'Ambil di tempat'];
        $paymentMethods = ['Cash', 'Bank Transfer', 'OVO', 'Dana', 'COD'];

        // Buat array cart berdasarkan grup product_id + exp_date
        $mergedCart = $cartItems->map(function ($groupedItems) use ($userId) {
            $firstItem = $groupedItems->first();
            $stock = ProductStock::where('product_id', $firstItem->product_id)
                ->where('exp_date', $firstItem->exp_date)
                ->value('stock') ?? 0;

            return [
                'id' => $firstItem->id,
                'user_id' => $userId,
                'product_id' => $firstItem->product_id,
                'product_name' => $firstItem->product->name,
                'product_image' => $firstItem->product->photo,
                'product_price' => $firstItem->product->price,
                'expired_date' => $firstItem->exp_date,
                'quantity' => $groupedItems->sum('quantity'),
                'gross_amount' => $groupedItems->sum('gross_amount'),
                'shipping_method' => $firstItem->shipping_method,
                'payment_method' => $firstItem->payment_method,
                'stock' => $stock,
            ];
        })->values();

        // Susun daftar exp_date dan stok untuk setiap product_id
        $productExpDateStock = $productStockList->map(function ($stocks) {
            return $stocks->map(function ($stock) {
                return [
                    'exp_date' => $stock->exp_date,
                    'stock' => $stock->stock,
                ];
            })->values();
        });

        return response()->json([
            'success' => true,
            'cart' => $mergedCart,
            'shipping_methods' => $shippingMethods,
            'payment_methods' => $paymentMethods,
            'product_exp_dates' => $productExpDateStock, // tambahan baru
        ], 200);
    }

    // Fungsi untuk update metode pengiriman, dan metode pembayaran
    public function updateField(Request $request)
    {
        $user = Auth::user(); // atau sesuaikan dengan otentikasi kamu
        $field = $request->input('field');
        $value = $request->input('value');

        $validFields = ['shipping_method', 'payment_method'];

        if (!in_array($field, $validFields)) {
            return response()->json(['message' => 'Field tidak valid'], 422);
        }

        // Update semua cart milik user, bisa disesuaikan dengan id cart jika perlu
        Cart::where('user_id', $user->id)->update([$field => $value]);

        // Ambil semua cart item milik user
        $cartItems = Cart::where('user_id', $user->id)->get();
        // $cartItems = Cart::all();

        foreach ($cartItems as $cart) {
            $cart->$field = $value;
            $cart->save();
        }

        return response()->json(['message' => 'Berhasil memperbarui data'], 200);
    }
}