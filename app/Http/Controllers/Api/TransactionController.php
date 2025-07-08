<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TransactionStatusHistory;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Container\Attributes\DB as AttributesDB;

class TransactionController extends Controller
{
    // Menampilkan semua transaksi milik user yang login
    // public function index()
    // {
    //     try {
    //         $user = Auth::user();
    //         $transactions = Transaction::with(['details.product', 'statusHistories'])
    //             ->where('user_id', $user->id)
    //             ->get();

    //         // Tambahkan estimasi shipping_time untuk setiap transaksi
    //         // $transactions = $transactions->map(function ($transaction) {
    //         //     $transaction->shipping_time = $this->calculateShippingTime($transaction);
    //         //     return $transaction;
    //         // });

    //         $transactions = $transactions->map(function ($transaction) {
    //             $transaction->shipping_time = $this->calculateShippingTime($transaction);

    //             $transaction->products = $transaction->details->map(function ($detail) {
    //                 return [
    //                     'product_id' => $detail->product->id,
    //                     'name' => $detail->product->name,
    //                     'code' => $detail->product->code,
    //                     'price' => $detail->product->price,
    //                     'quantity' => $detail->quantity,
    //                     'exp_date' => $detail->exp_date,
    //                     'photo' => $detail->product->photo,
    //                     // 'photo' => url('storage/' . $detail->product->photo),
    //                 ];
    //             });

    //             return $transaction;
    //         });

    //         return response()->json(['transactions' => $transactions], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Terjadi kesalahan saat mengambil data transaksi: ' . $e->getMessage()], 500);
    //     }
    // }

    // Menampilkan semua transaksi milik user yang login
    public function index()
    {
        try {
            $user = Auth::user();

            if ($user->usertype === 'admin') {
                // Jika admin, ambil semua transaksi
                $transactions = Transaction::with(['details.product', 'statusHistories'])->get();
            } else {
                // Selain admin, ambil hanya transaksi milik user tersebut
                $transactions = Transaction::with(['details.product', 'statusHistories'])
                    ->where('user_id', $user->id)
                    ->get();
            }

            $transactions = $transactions->map(function ($transaction) {
                $transaction->shipping_time = $this->calculateShippingTime($transaction);

                $transaction->products = $transaction->details->map(function ($detail) {
                    return [
                        'product_id' => $detail->product->id,
                        'name' => $detail->product->name,
                        'code' => $detail->product->code,
                        'price' => $detail->product->price,
                        'quantity' => $detail->quantity,
                        'exp_date' => $detail->exp_date,
                        'photo' => $detail->product->photo,
                    ];
                });

                return $transaction;
            });

            return response()->json(['transactions' => $transactions], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data transaksi: ' . $e->getMessage()], 500);
        }
    }

    // Menampilkan detail transaksi berdasarkan ID dan user
    // public function show($id)
    // {
    //     try {
    //         $user = Auth::user();
    //         $transaction = Transaction::with(['details.product', 'statusHistories', 'user'])
    //             ->where('id', $id)
    //             ->where('user_id', $user->id)
    //             ->firstOrFail();

    //         $transaction->shipping_time = $this->calculateShippingTime($transaction);

    //         $details = $transaction->products = $transaction->details->map(function ($detail) {
    //             return [
    //                 // 'product_id' => $detail->product->id,
    //                 // 'product_name' => $detail->product->name,
    //                 // 'product_code' => $detail->product->code,
    //                 // 'product_price' => $detail->product->price,
    //                 // 'quantity' => $detail->quantity,
    //                 // 'stock_before' => $detail->stock_before,
    //                 // 'stock_after' => $detail->stock_after,
    //                 // 'exp_date' => $detail->exp_date,
    //                 // 'photo' => $detail->product->photo,

    //                 'id' => $detail->id,
    //                 'transaction_id' => $detail->transaction_id,
    //                 'product_id' => $detail->product_id,
    //                 'quantity' => $detail->quantity,
    //                 'exp_date' => $detail->exp_date,
    //                 'product_name' => $detail->product_name,
    //                 'product_code' => $detail->product_code,
    //                 'product_price' => $detail->product_price,
    //                 // 'product_photo' => $detail->product_photo,
    //                 'product_photo' => $detail->product->photo,
    //                 'stock_before' => $detail->stock_before,
    //                 'stock_after' => $detail->stock_after,
    //             ];
    //         });

    //         return response()->json([
    //             'transaction' => [
    //                 'id' => $transaction->id,
    //                 'user_id' => $transaction->user_id,
    //                 'user_name' => $transaction->user->name,
    //                 'transaction_code' => $transaction->transaction_code,
    //                 'status' => $transaction->status,
    //                 'gross_amount' => $transaction->gross_amount,
    //                 'shipping_cost' => $transaction->shipping_cost,
    //                 'total_payment' => $transaction->total_payment,
    //                 'shipping_method' => $transaction->shipping_method,
    //                 'payment_method' => $transaction->payment_method,
    //                 'shipping_time' => $this->calculateShippingTime($transaction),
    //                 'transaction_date' => $transaction->transaction_date,
    //             ],
    //             'products' => $details,
    //             'status_histories' => $transaction->statusHistories
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Transaksi tidak ditemukan atau Anda tidak memiliki akses.'], 404);
    //     }
    // }

    // Menampilkan detail transaksi berdasarkan ID dan user
    public function show($id)
    {
        try {
            $user = Auth::user();

            if ($user->usertype === 'admin') {
                // Admin bisa akses transaksi manapun berdasarkan ID
                $transaction = Transaction::with(['details.product', 'statusHistories', 'user'])
                    ->where('id', $id)
                    ->firstOrFail();
            } else {
                // Selain admin, hanya boleh akses transaksi miliknya sendiri
                $transaction = Transaction::with(['details.product', 'statusHistories', 'user'])
                    ->where('id', $id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
            }

            $transaction->shipping_time = $this->calculateShippingTime($transaction);

            $details = $transaction->products = $transaction->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'transaction_id' => $detail->transaction_id,
                    'product_id' => $detail->product_id,
                    'quantity' => $detail->quantity,
                    'exp_date' => $detail->exp_date,
                    'product_name' => $detail->product_name,
                    'product_code' => $detail->product_code,
                    'product_price' => $detail->product_price,
                    'product_photo' => $detail->product->photo,
                    'stock_before' => $detail->stock_before,
                    'stock_after' => $detail->stock_after,
                ];
            });

            return response()->json([
                'transaction' => [
                    'id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'user_name' => $transaction->user->name,
                    'transaction_code' => $transaction->transaction_code,
                    'status' => $transaction->status,
                    'gross_amount' => $transaction->gross_amount,
                    'shipping_cost' => $transaction->shipping_cost,
                    'total_payment' => $transaction->total_payment,
                    'shipping_method' => $transaction->shipping_method,
                    'payment_method' => $transaction->payment_method,
                    'shipping_time' => $transaction->shipping_time,
                    'transaction_date' => $transaction->transaction_date,
                ],
                'products' => $details,
                'status_histories' => $transaction->statusHistories
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Transaksi tidak ditemukan atau Anda tidak memiliki akses.'], 404);
        }
    }

    // Fungsi update status transaksi
    // public function updateStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required|string'
    //     ]);

    //     $user = Auth::user();
    //     $transaction = Transaction::where('id', $id)->where('user_id', $user->id)->firstOrFail();

    //     // Cek apakah sudah final save
    //     if ($transaction->is_final == "Sudah selesai") {
    //         return response()->json(['error' => 'Transaksi ini sudah difinalisasi dan tidak dapat diubah lagi.'], 403);
    //     }

    //     // Update status saat ini
    //     $transaction->status = $request->status;
    //     $transaction->save();

    //     // Simpan ke riwayat
    //     TransactionStatusHistory::create([
    //         'transaction_id' => $transaction->id,
    //         'status' => $request->status,
    //     ]);

    //     return response()->json(['message' => 'Status transaksi berhasil diperbarui.'], 200);
    // }

    // Fungsi untuk menambah riwayat status transaksi
    public function addStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $user = Auth::user();
        $transaction = Transaction::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        // Cek apakah sudah final save
        if ($transaction->is_final == "Sudah selesai") {
            return response()->json(['error' => 'Transaksi ini sudah difinalisasi dan tidak dapat diubah lagi.'], 403);
        }

        // Cek apakah status yang sama sudah ada di riwayat
        $existingStatus = TransactionStatusHistory::where('transaction_id', $transaction->id)
            ->where('status', $request->status)
            ->exists();

        if ($existingStatus) {
            return response()->json(['error' => 'Status ini sudah pernah ditambahkan ke riwayat transaksi.'], 409);
        }

        // Update status saat ini
        $transaction->status = $request->status;
        $transaction->save();

        // Simpan ke riwayat
        TransactionStatusHistory::create([
            'transaction_id' => $transaction->id,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Status transaksi berhasil diperbarui.'], 200);
    }


    // Menampilkan seluruh riwayat status transaksi berdasarkan transaction_id
    public function showStatusHistory($transactionId)
    {
        $user = Auth::user();

        $transaction = Transaction::where('id', $transactionId)->where('user_id', $user->id)->firstOrFail();

        $statusHistories = TransactionStatusHistory::where('transaction_id', $transaction->id)
            ->orderBy('changed_at', 'asc')
            ->get();

        return response()->json($statusHistories);
    }

    // Mengedit salah satu status history
    public function editStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $statusHistory = TransactionStatusHistory::findOrFail($id);

        $transaction = Transaction::where('id', $statusHistory->transaction_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($transaction->is_final == "Sudah selesai") {
            return response()->json(['error' => 'Transaksi sudah difinalisasi dan tidak dapat diubah.'], 403);
        }

        $statusHistory->status = $request->status;
        $statusHistory->save();

        return response()->json(['message' => 'Status riwayat berhasil diperbarui.']);
    }

    // Menghapus salah satu status history
    // public function deleteStatus(Request $request, $id)
    // {
    //     $statusHistory = TransactionStatusHistory::findOrFail($id);

    //     $transaction = Transaction::where('id', $statusHistory->transaction_id)
    //         ->where('user_id', Auth::id())
    //         ->firstOrFail();

    //     if ($transaction->is_final == "Sudah selesai") {
    //         return response()->json(['error' => 'Transaksi sudah difinalisasi dan tidak dapat dihapus.'], 403);
    //     }

    //     $statusHistory->delete();

    //     return response()->json(['message' => 'Status riwayat berhasil dihapus.']);
    // }

    // public function deleteStatus(Request $request, $id)
    // {
    //     $statusHistory = TransactionStatusHistory::findOrFail($id);

    //     $transaction = Transaction::where('id', $statusHistory->transaction_id)
    //         ->where('user_id', Auth::id())
    //         ->firstOrFail();

    //     if ($transaction->is_final === "Sudah selesai") {
    //         return response()->json(['error' => 'Transaksi sudah difinalisasi dan tidak dapat dihapus.'], 403);
    //     }

    //     // Hapus riwayat status
    //     $statusHistory->delete();

    //     // Ambil status terbaru setelah penghapusan (berdasarkan waktu terbaru)
    //     $latestStatus = TransactionStatusHistory::where('transaction_id', $transaction->id)
    //         ->orderByDesc('changed_at')
    //         ->first();

    //     if ($latestStatus) {
    //         // Update kolom status pada tabel transactions
    //         $transaction->status = $latestStatus->status;
    //         $transaction->save();
    //     } else {
    //         // Jika tidak ada status history tersisa, bisa kosongkan atau default-kan status transaksi
    //         $transaction->status = null; // atau 'Menunggu', tergantung logika aplikasi Anda
    //         $transaction->save();
    //     }

    //     return response()->json(['message' => 'Status riwayat berhasil dihapus dan status transaksi diperbarui.']);
    // }

    // Fungsi untuk menghapus status transaksi
    public function deleteStatus(Request $request, $id)
    {
        $statusHistory = TransactionStatusHistory::findOrFail($id);

        $transaction = Transaction::where('id', $statusHistory->transaction_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($transaction->is_final === "Sudah selesai") {
            return response()->json(['error' => 'Transaksi sudah difinalisasi dan tidak dapat dihapus.'], 403);
        }

        // Hitung jumlah riwayat status yang dimiliki transaksi ini
        $statusCount = TransactionStatusHistory::where('transaction_id', $transaction->id)->count();

        if ($statusCount <= 1) {
            return response()->json(['error' => 'Penghapusan tidak dapat dilakukan karena minimal harus ada 1 riwayat status.'], 400);
        }

        // Hapus riwayat status
        $statusHistory->delete();

        // Ambil status terbaru setelah penghapusan (berdasarkan waktu terbaru)
        $latestStatus = TransactionStatusHistory::where('transaction_id', $transaction->id)
            ->orderByDesc('changed_at')
            ->first();

        if ($latestStatus) {
            $transaction->status = $latestStatus->status;
        } else {
            $transaction->status = null; // atau 'Menunggu', tergantung kebutuhan
        }

        $transaction->save();

        return response()->json(['message' => 'Status riwayat berhasil dihapus dan status transaksi diperbarui.']);
    }

    // public function getAvailableStatuses()
    // {
    //     if (!Auth::check()) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     // Ambil enum column dari database (pastikan nama tabel dan kolom sesuai ya)
    //     $type = DB::select(DB::raw("SHOW COLUMNS FROM transaction_status_histories WHERE Field = 'status'"))[0]->Type;

    //     // Ekstrak enum values dari hasil query
    //     preg_match('/^enum\((.*)\)$/', $type, $matches);

    //     if (!isset($matches[1])) {
    //         return response()->json([], 200);
    //     }

    //     $statuses = array_map(function ($value) {
    //         return trim($value, "'");
    //     }, explode(',', $matches[1]));

    //     return response()->json($statuses);
    // }

    // Fungsi Final Save dari riwayat status transaksi yang terjadi
    public function finalSave($id)
    {
        $user = Auth::user();
        $transaction = Transaction::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        if ($transaction->status !== 'Pesanan selesai') {
            return response()->json(['error' => 'Transaksi belum selesai. Finalisasi hanya dapat dilakukan jika status adalah "Pesanan selesai".'], 400);
        }

        if ($transaction->is_final == "Sudah selesai") {
            return response()->json(['message' => 'Transaksi ini sudah difinalisasi sebelumnya.'], 200);
        }

        // Tandai sebagai final
        $transaction->is_final = 'Sudah selesai';
        $transaction->save();

        return response()->json(['message' => 'Transaksi berhasil difinalisasi. Tidak dapat diubah lagi.'], 200);
    }

    // Mengecek apakah status terakhir transaksi sudah selesai atau belum
    public function checkFinalStatus($id)
    {
        $user = Auth::user();
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'is_final' => $transaction->is_final === 'Sudah selesai'
        ]);
    }

    // public function adminShow()
    // {
    //     try {
    //         $transactions = Transaction::with(['details.product', 'statusHistories'])
    //             ->get();

    //         // Tambahkan estimasi shipping_time untuk setiap transaksi
    //         // $transactions = $transactions->map(function ($transaction) {
    //         //     $transaction->shipping_time = $this->calculateShippingTime($transaction);
    //         //     return $transaction;
    //         // });

    //         $transactions = $transactions->map(function ($transaction) {
    //             $transaction->shipping_time = $this->calculateShippingTime($transaction);

    //             $transaction->products = $transaction->details->map(function ($detail) {
    //                 return [
    //                     'product_id' => $detail->product->id,
    //                     'name' => $detail->product->name,
    //                     'code' => $detail->product->code,
    //                     'price' => $detail->product->price,
    //                     'quantity' => $detail->quantity,
    //                     'exp_date' => $detail->exp_date,
    //                     'photo' => $detail->product->photo,
    //                     // 'photo' => url('storage/' . $detail->product->photo),
    //                 ];
    //             });

    //             return $transaction;
    //         });

    //         return response()->json(['transactions' => $transactions], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Terjadi kesalahan saat mengambil data transaksi: ' . $e->getMessage()], 500);
    //     }
    // }

    // Fungsi untuk menampilkan data 
    // public function adminDetailShow($id)
    // {
    //     try {
    //         $transaction = Transaction::with(['details.product', 'statusHistories', 'user'])
    //             ->where('id', $id)
    //             ->firstOrFail();

    //         $transaction->shipping_time = $this->calculateShippingTime($transaction);

    //         $details = $transaction->products = $transaction->details->map(function ($detail) {
    //             return [
    //                 // 'product_id' => $detail->product->id,
    //                 // 'product_name' => $detail->product->name,
    //                 // 'product_code' => $detail->product->code,
    //                 // 'product_price' => $detail->product->price,
    //                 // 'quantity' => $detail->quantity,
    //                 // 'stock_before' => $detail->stock_before,
    //                 // 'stock_after' => $detail->stock_after,
    //                 // 'exp_date' => $detail->exp_date,
    //                 // 'photo' => $detail->product->photo,

    //                 'id' => $detail->id,
    //                 'transaction_id' => $detail->transaction_id,
    //                 'product_id' => $detail->product_id,
    //                 'quantity' => $detail->quantity,
    //                 'exp_date' => $detail->exp_date,
    //                 'product_name' => $detail->product_name,
    //                 'product_code' => $detail->product_code,
    //                 'product_price' => $detail->product_price,
    //                 'product_photo' => $detail->product->photo,
    //                 // 'product_photo' => $detail->product_photo,
    //                 'stock_before' => $detail->stock_before,
    //                 'stock_after' => $detail->stock_after,
    //             ];
    //         });

    //         return response()->json([
    //             'transaction' => [
    //                 'id' => $transaction->id,
    //                 'user_id' => $transaction->user_id,
    //                 'user_name' => $transaction->user->name,
    //                 'transaction_code' => $transaction->transaction_code,
    //                 'status' => $transaction->status,
    //                 'gross_amount' => $transaction->gross_amount,
    //                 'shipping_cost' => $transaction->shipping_cost,
    //                 'total_payment' => $transaction->total_payment,
    //                 'shipping_method' => $transaction->shipping_method,
    //                 'payment_method' => $transaction->payment_method,
    //                 'shipping_time' => $this->calculateShippingTime($transaction),
    //                 'transaction_date' => $transaction->transaction_date,
    //             ],
    //             'products' => $details,
    //             'status_histories' => $transaction->statusHistories
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Transaksi tidak ditemukan atau Anda tidak memiliki akses.'], 404);
    //     }
    // }

    // Fungsi bantu untuk estimasi waktu pengiriman
    private function calculateShippingTime($transaction)
    {
        if (!$transaction->transaction_date || !$transaction->shipping_method) {
            return null;
        }

        // $transactionTime = Carbon::parse($transaction->transaction_date);
        $transactionTime = Carbon::parse($transaction->transaction_date)->timezone('Asia/Jakarta');


        if ($transaction->shipping_method === 'Reguler') {
            // $start = $transactionTime->copy()->addHours(3);
            $start = $transactionTime->copy()->addMinutes(30);
            $end = $start->copy()->addMinutes(60);
            return 'Waktu Pengiriman : ' . $start->translatedFormat('d F Y, H:i') . ' - ' . $end->translatedFormat('H:i');
        } elseif ($transaction->shipping_method === 'Express') {
            // $start = $transactionTime->copy()->addHour();
            $start = $transactionTime->copy()->addMinutes(30);
            $end = $start->copy()->addMinutes(180);
            return 'Waktu Pengiriman : ' . $start->translatedFormat('d F Y, H:i') . ' - ' . $end->translatedFormat('H:i');
        } elseif ($transaction->shipping_method === 'Ambil di tempat') {
            $start = $transactionTime->copy();
            $end = $start->copy()->addMinutes(0);
            // return 'Waktu Pengiriman : ' . $start->translatedFormat('d F Y, H:i');
            return 'Waktu Pengiriman : - ';
        } else {
            return null;
        }

        // return 'Waktu Pengiriman : ' . $start->translatedFormat('d F Y, H:i') . ' - ' . $end->translatedFormat('H:i');
    }

    // Untuk menampilkan data pada dashboard
    public function dashboard(Request $request)
    {
        $monthName = $request->input('month', Carbon::now()->locale('id')->translatedFormat('F'));
        $year = $request->input('year', Carbon::now()->year); // Ambil tahun dari request

        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12,
        ];

        $monthNumber = $bulanMap[$monthName] ?? null;
        if (!$monthNumber) {
            return response()->json(['error' => 'Bulan tidak valid.'], 400);
        }

        // Gunakan tahun dari input
        $startDate = Carbon::createFromDate($year, $monthNumber, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $monthNumber, 1)->endOfMonth();

        // Total pendapatan dan transaksi bulan + tahun terpilih
        $totalRevenue = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_payment');
        $totalTransactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->count();

        $totalProducts = Product::count();

        $topProducts = TransactionDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'products.photo',
                'categories.name as category_name',
                FacadesDB::raw('SUM(transaction_details.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'categories.name', 'products.price', 'products.photo')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();

        // Grafik penjualan (8 bulan terakhir dari current date)
        $salesByMonth = Transaction::whereBetween('transaction_date', [Carbon::now()->subMonths(8), Carbon::now()])
            ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, SUM(gross_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'total_products' => $totalProducts,
            'top_products' => $topProducts,
            'sales_by_month' => $salesByMonth,
            'selected_month' => $monthName,
            'selected_year' => (int) $year,
        ]);
    }
}


// namespace App\Http\Controllers\Api;

// use App\Models\Product;
// use App\Models\Transaction;
// use Illuminate\Http\Request;
// use App\Models\TransactionDetail;
// use Illuminate\Support\Facades\DB;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Auth;
// use App\Models\TransactionStatusHistory;

// class TransactionApiController extends Controller
// {
//     // Menampilkan semua transaksi milik user yang login
//     public function index()
//     {
//         try {
//             $user = Auth::user();
//             $transactions = Transaction::with(['details.product', 'statusHistories'])
//                 ->where('user_id', $user->id)
//                 ->get();

//             return response()->json(['transactions' => $transactions], 200);
//         } catch (\Exception $e) {
//             return response()->json(['error' => 'Terjadi kesalahan saat mengambil data transaksi: ' . $e->getMessage()], 500);
//         }
//     }

//     // Menampilkan detail transaksi berdasarkan ID dan user
//     public function show($id)
//     {
//         try {
//             $user = Auth::user();
//             $transaction = Transaction::with(['details.product', 'statusHistories'])
//                 ->where('id', $id)
//                 ->where('user_id', $user->id)
//                 ->firstOrFail();

//             $details = $transaction->details->map(function ($detail) {
//                 return [
//                     'product_id' => $detail->product->id,
//                     'name' => $detail->product->name,
//                     'code' => $detail->product->code,
//                     'price' => $detail->product->price,
//                     'quantity' => $detail->quantity,
//                     'exp_date' => $detail->exp_date,
//                     'photo' => $detail->product->photo,
//                 ];
//             });

//             return response()->json([
//                 'transaction' => [
//                     'id' => $transaction->id,
//                     'transaction_code' => $transaction->transaction_code,
//                     'status' => $transaction->status,
//                     'gross_amount' => $transaction->gross_amount,
//                     'shipping_cost' => $transaction->shipping_cost,
//                     'total_payment' => $transaction->total_payment,
//                     'shipping_method' => $transaction->shipping_method,
//                     'payment_method' => $transaction->payment_method,
//                     'shipping_time' => $transaction->shipping_time,
//                     'transaction_date' => $transaction->transaction_date,
//                 ],
//                 'products' => $details,
//                 'status_histories' => $transaction->statusHistories
//             ], 200);
//         } catch (\Exception $e) {
//             return response()->json(['error' => 'Transaksi tidak ditemukan atau Anda tidak memiliki akses.'], 404);
//         }
//     }

//     // Fungsi update status transaksi
//     public function updateStatus(Request $request, $id)
//     {
//         $request->validate([
//             'status' => 'required|string'
//         ]);

//         $user = Auth::user();
//         $transaction = Transaction::where('id', $id)->where('user_id', $user->id)->firstOrFail();

//         // Cek apakah sudah final save
//         if ($transaction->is_final) {
//             return response()->json(['error' => 'Transaksi ini sudah difinalisasi dan tidak dapat diubah lagi.'], 403);
//         }

//         // Update status saat ini
//         $transaction->status = $request->status;
//         $transaction->save();

//         // Simpan ke riwayat
//         TransactionStatusHistory::create([
//             'transaction_id' => $transaction->id,
//             'status' => $request->status,
//         ]);

//         return response()->json(['message' => 'Status transaksi berhasil diperbarui.'], 200);
//     }

//     // Fungsi Final Save
//     public function finalSave($id)
//     {
//         $user = Auth::user();
//         $transaction = Transaction::where('id', $id)->where('user_id', $user->id)->firstOrFail();

//         if ($transaction->status !== 'Pesanan selesai') {
//             return response()->json(['error' => 'Transaksi belum selesai. Finalisasi hanya dapat dilakukan jika status adalah "Pesanan selesai".'], 400);
//         }

//         if ($transaction->is_final) {
//             return response()->json(['message' => 'Transaksi ini sudah difinalisasi sebelumnya.'], 200);
//         }

//         // Tandai sebagai final
//         $transaction->is_final = true;
//         $transaction->save();

//         return response()->json(['message' => 'Transaksi berhasil difinalisasi. Tidak dapat diubah lagi.'], 200);
//     }
// }


// class TransactionApiController extends Controller
// {
//     // Menampilkan semua transaksi beserta detail produk
//     public function index()
//     {
//         try {
//             $transactions = Transaction::with(['details.product', 'statusHistories'])->get();
//             return response()->json(['transactions' => $transactions], 200);
//         } catch (\Exception $e) {
//             return response()->json(['error' => 'Terjadi kesalahan saat mengambil data transaksi: ' . $e->getMessage()], 500);
//         }
//     }

//     // Menampilkan detail transaksi berdasarkan ID
//     public function show($id)
//     {
//         try {
//             $transaction = Transaction::with(['details.product', 'statusHistories'])->findOrFail($id);

//             $details = $transaction->details->map(function ($detail) {
//                 return [
//                     'product_id' => $detail->product->id,
//                     'name' => $detail->product->name,
//                     'code' => $detail->product->code,
//                     'price' => $detail->product->price,
//                     'quantity' => $detail->quantity,
//                     'exp_date' => $detail->exp_date,
//                     'photo' => $detail->product->photo,
//                 ];
//             });

//             return response()->json([
//                 'transaction' => [
//                     'id' => $transaction->id,
//                     'transaction_code' => $transaction->transaction_code,
//                     'status' => $transaction->status,
//                     'gross_amount' => $transaction->gross_amount,
//                     'shipping_cost' => $transaction->shipping_cost,
//                     'total_payment' => $transaction->total_payment,
//                     'shipping_method' => $transaction->shipping_method,
//                     'payment_method' => $transaction->payment_method,
//                     'shipping_time' => $transaction->shipping_time,
//                     'transaction_date' => $transaction->transaction_date,
//                 ],
//                 'products' => $details,
//                 'status_histories' => $transaction->statusHistories
//             ], 200);
//         } catch (\Exception $e) {
//             return response()->json(['error' => 'Transaksi tidak ditemukan: ' . $e->getMessage()], 404);
//         }
//     }

//     // Membuat transaksi baru
//     // public function store(Request $request)
//     // {
//     //     $validatedData = $request->validate([
//     //         'user_id' => 'required|exists:users,id',
//     //         'transaction_code' => 'required',
//     //         'shipping_cost' => 'required|numeric|min:0',
//     //         'shipping_method' => 'nullable|in:Reguler,Express,Ambil di tempat',
//     //         'payment_method' => 'required|in:Cash,Bank Transfer,OVO,Dana,COD',
//     //         'products' => 'required|array',
//     //         'products.*.id' => 'required|exists:products,id',
//     //         'products.*.quantity' => 'required|integer|min:1',
//     //     ]);

//     //     DB::beginTransaction();

//     //     try {
//     //         $grossAmount = 0;

//     //         // Hitung total gross amount dan total payment
//     //         foreach ($validatedData['products'] as $productData) {
//     //             $product = Product::findOrFail($productData['id']);

//     //             if ($product->stock < $productData['quantity']) {
//     //                 return response()->json([
//     //                     'success' => false,
//     //                     'message' => 'Stok tidak mencukupi untuk produk ' . $product->name
//     //                 ], 400);
//     //             }

//     //             $grossAmount += $product->price * $productData['quantity'];
//     //         }

//     //         $totalPayment = $grossAmount + $validatedData['shipping_cost'];

//     //         // Buat transaksi
//     //         $transaction = Transaction::create([
//     //             'user_id' => $validatedData['user_id'],
//     //             'transaction_code' => 'TRX-' . strtoupper(uniqid()),
//     //             'gross_amount' => $grossAmount,
//     //             'shipping_cost' => $validatedData['shipping_cost'],
//     //             'total_payment' => $totalPayment,
//     //             'shipping_method' => $validatedData['shipping_method'] ?? null,
//     //             'payment_method' => $validatedData['payment_method'],
//     //             'status' => 'Dalam pengemasan',
//     //             'transaction_date' => now(),
//     //             'shipping_time' => now()->addDays(3), // contoh estimasi pengiriman
//     //         ]);

//     //         // Simpan setiap detail transaksi
//     //         foreach ($validatedData['products'] as $productData) {
//     //             $product = Product::findOrFail($productData['id']);

//     //             $stockBefore = $product->stock;
//     //             $stockAfter = $product->stock - $productData['quantity'];

//     //             // Kurangi stok
//     //             $product->stock = $stockAfter;
//     //             $product->save();

//     //             $transaction->details()->create([
//     //                 'product_id' => $product->id,
//     //                 'quantity' => $productData['quantity'],
//     //                 'exp_date' => $product->exp_date ?? null,
//     //                 'product_name' => $product->name,
//     //                 'product_code' => $product->code,
//     //                 'product_price' => $product->price,
//     //                 'stock_before' => $stockBefore,
//     //                 'stock_after' => $stockAfter,
//     //             ]);
//     //         }

//     //         // Catat status awal transaksi
//     //         TransactionStatusHistory::create([
//     //             'transaction_id' => $transaction->id,
//     //             'status' => $transaction->status,
//     //             'changed_at' => now(),
//     //         ]);

//     //         DB::commit();

//     //         return response()->json([
//     //             'success' => true,
//     //             'transaction' => $transaction->load('details', 'statusHistories')
//     //         ], 201);
//     //     } catch (\Exception $e) {
//     //         DB::rollBack();
//     //         return response()->json(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()], 500);
//     //     }
//     // }

//     // Menghapus transaksi berdasarkan ID
//     // public function destroy($id)
//     // {
//     //     try {
//     //         $transaction = Transaction::findOrFail($id);

//     //         // Restore stok produk sebelum hapus transaksi
//     //         foreach ($transaction->details as $detail) {
//     //             $product = $detail->product;
//     //             $product->stock += $detail->quantity;
//     //             $product->save();
//     //         }

//     //         $transaction->delete();

//     //         return response()->json(['success' => 'Transaksi berhasil dihapus'], 200);
//     //     } catch (\Exception $e) {
//     //         return response()->json(['error' => 'Gagal menghapus transaksi: ' . $e->getMessage()], 500);
//     //     }
//     // }
// }
// }
