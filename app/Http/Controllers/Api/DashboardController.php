<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB as FacadesDB;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     // Total pendapatan dari semua transaksi
    //     $totalRevenue = Transaction::sum('gross_amount');

    //     // Jumlah transaksi
    //     $totalTransactions = Transaction::count();

    //     // Total produk
    //     $totalProducts = Product::count();

    //     // Top 3 produk terlaris berdasarkan jumlah yang terjual
    //     $topProducts = TransactionDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
    //         ->select('products.name', 'products.price', 'products.photo', FacadesDB::raw('SUM(transaction_details.quantity) as total_sold'))
    //         ->groupBy('products.id', 'products.name')
    //         ->orderByDesc('total_sold')
    //         ->limit(3)
    //         ->get();

    //     // Grafik penjualan per bulan (12 bulan terakhir)
    //     // $salesByMonth = Transaction::whereBetween('transaction_date', [Carbon::now()->subMonths(12), Carbon::now()])
    //     //     ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, SUM(gross_amount) as revenue')
    //     //     ->groupBy('month')
    //     //     ->orderBy('month')
    //     //     ->get();

    //     // Grafik penjualan per bulan (8 bulan terakhir)
    //     $salesByMonth = Transaction::whereBetween('transaction_date', [Carbon::now()->subMonths(8), Carbon::now()])
    //         ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, SUM(gross_amount) as revenue')
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->get();

    //     return response()->json([
    //         'total_revenue' => $totalRevenue,
    //         'total_transactions' => $totalTransactions,
    //         'total_products' => $totalProducts,
    //         'top_products' => $topProducts,
    //         'sales_by_month' => $salesByMonth,
    //     ]);
    // }

    // public function index()
    // {
    //     // Total pendapatan dari semua transaksi
    //     $totalRevenue = Transaction::sum('total_payment');

    //     // Jumlah transaksi
    //     $totalTransactions = Transaction::count();

    //     // Total produk
    //     $totalProducts = Product::count();

    //     // Top 3 produk terlaris berdasarkan jumlah yang terjual dengan kategori
    //     $topProducts = TransactionDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
    //         ->join('categories', 'products.category_id', '=', 'categories.id')
    //         ->select(
    //             'products.id',
    //             'products.name',
    //             'products.price',
    //             'products.photo',
    //             'categories.name as category_name',
    //             FacadesDB::raw('SUM(transaction_details.quantity) as total_sold')
    //         )
    //         ->groupBy('products.id', 'products.name', 'categories.name')
    //         ->orderByDesc('total_sold')
    //         ->limit(3)
    //         ->get();

    //     // Grafik penjualan per bulan (8 bulan terakhir)
    //     $salesByMonth = Transaction::whereBetween('transaction_date', [Carbon::now()->subMonths(8), Carbon::now()])
    //         ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, SUM(gross_amount) as revenue')
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->get();

    //     return response()->json([
    //         'total_revenue' => $totalRevenue,
    //         'total_transactions' => $totalTransactions,
    //         'total_products' => $totalProducts,
    //         'top_products' => $topProducts,
    //         'sales_by_month' => $salesByMonth,
    //     ]);
    // }

    //     public function index(Request $request)
//     {
//         $month = $request->input('month', Carbon::now()->format('Y-m')); // Default: bulan sekarang (format: YYYY-MM)

    //         $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
//         $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    //         // Total pendapatan dan transaksi bulan terpilih
//         $totalRevenue = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_payment');
//         $totalTransactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->count();

    //         // Total produk (tidak terpengaruh bulan)
//         $totalProducts = Product::count();

    //         // Top produk tetap dari semua data
//         $topProducts = TransactionDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
//             ->join('categories', 'products.category_id', '=', 'categories.id')
//             ->select(
//                 'products.id',
//                 'products.name',
//                 'products.price',
//                 'products.photo',
//                 'categories.name as category_name',
//                 FacadesDB::raw('SUM(transaction_details.quantity) as total_sold')
//             )
//             ->groupBy('products.id', 'products.name', 'categories.name', 'products.price', 'products.photo')
//             ->orderByDesc('total_sold')
//             ->limit(3)
//             ->get();

    //         // Grafik penjualan tetap 8 bulan terakhir
//         $salesByMonth = Transaction::whereBetween('transaction_date', [Carbon::now()->subMonths(8), Carbon::now()])
//             ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, SUM(gross_amount) as revenue')
//             ->groupBy('month')
//             ->orderBy('month')
//             ->get();

    //         return response()->json([
//             'total_revenue' => $totalRevenue,
//             'total_transactions' => $totalTransactions,
//             'total_products' => $totalProducts,
//             'top_products' => $topProducts,
//             'sales_by_month' => $salesByMonth,
//             'selected_month' => $month,
//         ]);
//     }

    // public function index(Request $request)
    // {
    //     $monthName = $request->input('month', Carbon::now()->locale('id')->translatedFormat('F')); // default: bulan ini dalam format "Mei"

    //     // Mapping nama bulan Indonesia ke angka
    //     $bulanMap = [
    //         'Januari' => 1,
    //         'Februari' => 2,
    //         'Maret' => 3,
    //         'April' => 4,
    //         'Mei' => 5,
    //         'Juni' => 6,
    //         'Juli' => 7,
    //         'Agustus' => 8,
    //         'September' => 9,
    //         'Oktober' => 10,
    //         'November' => 11,
    //         'Desember' => 12,
    //     ];

    //     $monthNumber = $bulanMap[$monthName] ?? null;
    //     if (!$monthNumber) {
    //         return response()->json(['error' => 'Bulan tidak valid.'], 400);
    //     }

    //     $year = Carbon::now()->year;
    //     $startDate = Carbon::createFromDate($year, $monthNumber, 1)->startOfMonth();
    //     $endDate = Carbon::createFromDate($year, $monthNumber, 1)->endOfMonth();

    //     // Total pendapatan dan transaksi bulan terpilih
    //     $totalRevenue = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_payment');
    //     $totalTransactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->count();

    //     // Total produk
    //     $totalProducts = Product::count();

    //     // Top produk (semua data)
    //     $topProducts = TransactionDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
    //         ->join('categories', 'products.category_id', '=', 'categories.id')
    //         ->select(
    //             'products.id',
    //             'products.name',
    //             'products.price',
    //             'products.photo',
    //             'categories.name as category_name',
    //             FacadesDB::raw('SUM(transaction_details.quantity) as total_sold')
    //         )
    //         ->groupBy('products.id', 'products.name', 'categories.name', 'products.price', 'products.photo')
    //         ->orderByDesc('total_sold')
    //         ->limit(3)
    //         ->get();

    //     // Grafik penjualan (8 bulan terakhir)
    //     $salesByMonth = Transaction::whereBetween('transaction_date', [Carbon::now()->subMonths(8), Carbon::now()])
    //         ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, SUM(gross_amount) as revenue')
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->get();

    //     return response()->json([
    //         'total_revenue' => $totalRevenue,
    //         'total_transactions' => $totalTransactions,
    //         'total_products' => $totalProducts,
    //         'top_products' => $topProducts,
    //         'sales_by_month' => $salesByMonth,
    //         'selected_month' => $monthName,
    //     ]);
    // }
    public function index(Request $request)
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
