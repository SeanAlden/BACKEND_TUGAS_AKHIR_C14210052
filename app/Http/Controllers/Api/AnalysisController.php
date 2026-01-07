<?php

// ========== Percobaan 1 ==========

// namespace App\Http\Controllers\API;

// use App\Models\Product;
// use App\Models\SalesCount;
// use App\Models\EntropyGain;
// use App\Models\Transaction;
// use App\Models\DecisionTree;
// use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
// use App\Models\AccuracyPrediction;
// use App\Http\Controllers\Controller;

// class AnalysisController extends Controller
// {
//     // public function step1()~
//     // {
//     //     $transactions = Transaction::with('details.product')->get();
//     //     return response()->json(['transactions' => $transactions]);
//     // }

//     // public function step2()
//     // {
//     //     $transactions = Transaction::with('details.product')->get();
//     //     $Tmax = Carbon::parse(Transaction::max('transaction_date'));

//     //     $weightedSales = [];
//     //     $firstTransactionDates = [];
//     //     $lastTransactionDates = [];
//     //     $productDateDifferences = [];
//     //     $productTimeWeights = [];

//     //     foreach ($transactions as $transaction) {
//     //         $transactionDate = Carbon::parse($transaction->transaction_date);
//     //         $selisihHari = $transactionDate->diffInDays($Tmax);
//     //         $bobotWaktu = exp(-0.005 * $selisihHari);

//     //         foreach ($transaction->details as $detail) {
//     //             $productId = $detail->product->id;
//     //             $quantity = $detail->quantity;

//     //             if (!isset($firstTransactionDates[$productId]) || Carbon::parse($firstTransactionDates[$productId])->gt($transactionDate)) {
//     //                 $firstTransactionDates[$productId] = $transactionDate;
//     //             }

//     //             if (!isset($lastTransactionDates[$productId]) || Carbon::parse($lastTransactionDates[$productId])->lt($transactionDate)) {
//     //                 $lastTransactionDates[$productId] = $transactionDate;
//     //             }

//     //             if (!isset($weightedSales[$productId])) {
//     //                 $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
//     //             }

//     //             $weightedSales[$productId]['raw'] += $quantity;
//     //             $weightedSales[$productId]['weighted'] += $quantity * $bobotWaktu;
//     //         }
//     //     }

//     //     foreach ($weightedSales as $productId => &$sales) {
//     //         $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//     //         $lastTransactionDate = Carbon::parse($lastTransactionDates[$productId]);
//     //         $selisihHari = $firstTransactionDate->diffInDays($lastTransactionDate);
//     //         $bobotWaktuTambahan = log(1 + $selisihHari);

//     //         $productDateDifferences[$productId] = $selisihHari;
//     //         $productTimeWeights[$productId] = round($bobotWaktuTambahan, 4);
//     //         $sales['weighted'] *= $bobotWaktuTambahan;
//     //     }

//     //     return response()->json([
//     //         'transactions' => $transactions,
//     //         'weightedSales' => $weightedSales,
//     //         'firstTransactionDates' => $firstTransactionDates,
//     //         'lastTransactionDates' => $lastTransactionDates,
//     //         'productDateDifferences' => $productDateDifferences,
//     //         'productTimeWeights' => $productTimeWeights,
//     //         'Tmax' => $Tmax,
//     //     ]);
//     // }

//     // public function index()
//     // {
//     //     $transactions = Transaction::with('details.product')->get();
//     //     if ($transactions->isEmpty()) {
//     //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//     //     }

//     //     $tMax = Carbon::parse(Transaction::max('transaction_date'));
//     //     $lambda = 0.005;
//     //     $weightedSales = [];
//     //     $firstTransactionDates = [];

//     //     foreach ($transactions as $transaction) {
//     //         $t = Carbon::parse($transaction->transaction_date);
//     //         $diffDays = $t->diffInDays($tMax);
//     //         $weight = exp(-$lambda * $diffDays);

//     //         foreach ($transaction->details as $detail) {
//     //             $productId = $detail->product->id;
//     //             $quantity = $detail->quantity;

//     //             if (!isset($firstTransactionDates[$productId]) || Carbon::parse($firstTransactionDates[$productId])->gt($t)) {
//     //                 $firstTransactionDates[$productId] = $t;
//     //             }
//     //             $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
//     //         }
//     //     }

//     //     foreach ($weightedSales as $productId => &$sales) {
//     //         $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//     //         $productAge = $firstTransactionDate->diffInDays($tMax);
//     //         $sales *= log(1 + $productAge);
//     //     }

//     //     if (empty($weightedSales)) {
//     //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//     //     }

//     //     $maxWeightedSales = max($weightedSales);
//     //     $accuracy = collect($weightedSales)->map(fn($sales) => round(($sales / $maxWeightedSales) * 100, 2));

//     //     $totalSales = array_sum($weightedSales);
//     //     $entropyValues = [];
//     //     foreach ($weightedSales as $productId => $sales) {
//     //         $probability = $sales / $totalSales;
//     //         $entropyValues[$productId] = ($probability > 0) ? -($probability * log($probability, 2)) : 0;
//     //     }
//     //     $overallEntropy = array_sum($entropyValues);
//     //     $gainValues = [];
//     //     foreach ($entropyValues as $productId => $entropy) {
//     //         $gainValues[$productId] = $overallEntropy - $entropy;
//     //     }

//     //     return response()->json([
//     //         'accuracy' => $accuracy,
//     //         'entropyValues' => $entropyValues,
//     //         'gainValues' => $gainValues,
//     //         'decisionTree' => $this->buildDecisionTree($gainValues, $accuracy)
//     //     ]);
//     // }

//     // private function buildDecisionTree($gainValues, $accuracy)
//     // {
//     //     if (empty($gainValues)) {
//     //         return "Tidak ada decision tree.";
//     //     }

//     //     $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//     //     $tree = "Root Node: $bestAttribute\n";

//     //     foreach ($accuracy as $productId => $acc) {
//     //         $accuracyCategory = ($acc >= 80) ? "tinggi" : (($acc >= 50) ? "sedang" : "rendah");
//     //         $tree .= "|-- Produk ID $productId (Akurasi: $accuracyCategory)\n";
//     //     }
//     //     return $tree;
//     // }

//     public function step1()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         return response()->json(['transactions' => $transactions]);
//     }

//     public function step2()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         $Tmax = Carbon::parse(Transaction::max('transaction_date'));

//         $weightedSales = [];
//         $firstTransactionDates = [];
//         $lastTransactionDates = [];
//         $productDateDifferences = [];
//         $productTimeWeights = [];

//         foreach ($transactions as $transaction) {
//             $transactionDate = Carbon::parse($transaction->transaction_date);
//             $selisihHari = $transactionDate->diffInDays($Tmax);
//             $bobotWaktu = exp(-0.005 * $selisihHari);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity;

//                 if (!isset($firstTransactionDates[$productId]) || Carbon::parse($firstTransactionDates[$productId])->gt($transactionDate)) {
//                     $firstTransactionDates[$productId] = $transactionDate;
//                 }
//                 if (!isset($lastTransactionDates[$productId]) || Carbon::parse($lastTransactionDates[$productId])->lt($transactionDate)) {
//                     $lastTransactionDates[$productId] = $transactionDate;
//                 }
//                 if (!isset($weightedSales[$productId])) {
//                     $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
//                 }
//                 $weightedSales[$productId]['raw'] += $quantity;
//                 $weightedSales[$productId]['weighted'] += $quantity * $bobotWaktu;
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $lastTransactionDate = Carbon::parse($lastTransactionDates[$productId]);
//             $selisihHari = $firstTransactionDate->diffInDays($lastTransactionDate);
//             $bobotWaktuTambahan = log(1 + $selisihHari);

//             $productDateDifferences[$productId] = $selisihHari;
//             $productTimeWeights[$productId] = round($bobotWaktuTambahan, 4);
//             $sales['weighted'] *= $bobotWaktuTambahan;

//             SalesCount::updateOrCreate(
//                 ['product_id' => $productId, 'transaction_date' => $Tmax],
//                 [
//                     'raw_sales' => $sales['raw'],
//                     'weighted_sales' => $sales['weighted'],
//                     'days_between_first_last_transaction' => $productDateDifferences[$productId],
//                     'time_weight' => $productTimeWeights[$productId]
//                 ]
//             );
//         }

//         $products = Product::whereIn('id', array_keys($weightedSales))->get()->keyBy('id');

//         return response()->json(compact(
//             'transactions',
//             'weightedSales',
//             'products',
//             'firstTransactionDates',
//             'lastTransactionDates',
//             'productDateDifferences',
//             'productTimeWeights',
//             'Tmax'
//         ));
//     }

//     public function index()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         if ($transactions->isEmpty()) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         $tMax = Carbon::parse(Transaction::max('transaction_date'));
//         $lambda = 0.007;
//         $weightedSales = [];
//         $firstTransactionDates = [];

//         foreach ($transactions as $transaction) {
//             $t = Carbon::parse($transaction->transaction_date);
//             $diffDays = $t->diffInDays($tMax);
//             $weight = exp(-$lambda * $diffDays);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity;
//                 if (!isset($firstTransactionDates[$productId]) || Carbon::parse($firstTransactionDates[$productId])->gt($t)) {
//                     $firstTransactionDates[$productId] = $t;
//                 }
//                 $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $productAge = $firstTransactionDate->diffInDays($tMax);
//             $sales *= log(1 + $productAge);
//         }

//         if (empty($weightedSales)) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         $maxWeightedSales = max($weightedSales);
//         $accuracy = collect($weightedSales)->map(fn($sales) => round(($sales / $maxWeightedSales) * 100, 2));

//         $totalSales = array_sum($weightedSales);
//         $entropyValues = [];

//         foreach ($weightedSales as $productId => $sales) {
//             $probability = $sales / $totalSales;
//             $entropyValues[$productId] = ($probability > 0) ? - ($probability * log($probability, 2)) : 0;
//         }

//         $overallEntropy = array_sum($entropyValues);
//         $gainValues = [];

//         foreach ($entropyValues as $productId => $entropy) {
//             $gainValues[$productId] = $overallEntropy - $entropy;
//         }

//         foreach ($gainValues as $productId => $gain) {
//             EntropyGain::updateOrCreate(
//                 ['product_id' => $productId],
//                 ['entropy' => $entropyValues[$productId], 'gain' => $gain]
//             );
//         }

//         // $productsData = Product::whereIn('id', array_keys($weightedSales))->get();
//         // $products = $productsData->keyBy('id')->map(function ($product) {
//         //     return [
//         //         'name'  => $product->name,
//         //         'price' => $product->price,
//         //         'stock' => $product->stock,
//         //         'photo' => $product->photo,
//         //         'category_name' => $product->category->name
//         //     ];
//         // });

//         // Stok Per Expired
//         $productsData = Product::whereIn('id', array_keys($weightedSales))
//             ->with('stocks') // Pastikan model memiliki relasi ke tabel stok
//             ->get();

//         $products = $productsData->keyBy('id')->map(function ($product) {
//             return [
//                 'name'  => $product->name,
//                 'code'  => $product->code,
//                 'condition'  => $product->condition,
//                 'price' => $product->price,
//                 'photo' => $product->photo,
//                 'category_name' => $product->category->name,
//                 // 'stocks' => $product->stocks->map(function ($stock) {
//                 //     return [
//                 //         // 'expired_date' => $stock->expired_date,
//                 //         // 'amount' => $stock->amount
//                 //         'expired_date' => $stock->exp_date,
//                 //         'amount' => $stock->stock
//                 //     ];
//                 // })
//                 'stocks' => $product->stocks->sum('stock') // Menghitung total stok langsung

//             ];
//         });


//         $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

//         return response()->json(compact('accuracy', 'products', 'entropyValues', 'gainValues', 'decisionTree'));

//         // foreach ($accuracy as $productId => $acc) {
//         //     AccuracyPrediction::updateOrCreate(
//         //         ['product_id' => $productId],
//         //         ['accuracy_percentage' => $acc]
//         //     );
//         // }
//     }

//     // private function buildDecisionTree($gainValues, $accuracy, $products)
//     // {
//     //     if (empty($gainValues)) {
//     //         return "Tidak ada decision tree.";
//     //     }

//     //     $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//     //     $tree = "Root Node: $bestAttribute\n";

//     //     foreach ($accuracy as $productId => $acc) {
//     //         $product = $products[$productId] ?? null;
//     //         if (!$product) continue;

//     //         $tree .= "|-- *{$product['name']}* (Akurasi: $acc%)\n";
//     //     }
//     //     return $tree;
//     // }

//     private function buildDecisionTree($gainValues, $accuracy, $products)
//     {
//         if (empty($gainValues)) {
//             return "Tidak ada decision tree.";
//         }

//         $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//         $tree = "Root Node: $bestAttribute\n";

//         foreach ($accuracy as $productId => $acc) {
//             $product = $products[$productId] ?? null;
//             if (!$product) continue;

//             $productName = $product['name'];
//             $priceCategory = ($product['price'] > 150000) ? "tinggi" : (($product['price'] >= 50000) ? "sedang" : "rendah");
//             $stockCategory = ($product['stocks'] > 50) ? "tinggi" : (($product['stocks'] >= 10) ? "sedang" : "rendah");
//             $accuracyCategory = ($acc >= 80) ? "tinggi" : (($acc >= 50) ? "sedang" : "rendah");

//             $tree .= "|-- *$productName*
//         |---Akurasi: $accuracyCategory ($acc%)
//         |---Harga: $priceCategory
//         |---Stok: $stockCategory\n";
//             if ($accuracyCategory == "tinggi") {
//                 $tree .= "              ├─ Kondisi: Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.\n";
//             } elseif ($accuracyCategory == "sedang") {
//                 $tree .= "              ├─ Kondisi: Perlu strategi pemasaran lebih agresif.\n";
//             } else {
//                 $tree .= "              ├─ Kondisi: Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.\n";
//             }

//             $recommendation = ($accuracyCategory == "tinggi") ?
//                 "Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok." : (($accuracyCategory == "sedang") ? "Perlu strategi pemasaran lebih agresif." :
//                     "Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.");

//             DecisionTree::updateOrCreate(
//                 ['product_id' => $productId],
//                 [
//                     'accuracy_category' => $accuracyCategory,
//                     'price_category' => $priceCategory,
//                     'stock_category' => $stockCategory,
//                     'recommendation' => $recommendation
//                 ]
//             );

//             AccuracyPrediction::updateOrCreate(
//                 ['product_id' => $productId],
//                 ['accuracy_percentage' => $acc]
//             );
//         }
//         return $tree;
//     }

//     // private function buildDecisionTree($gainValues, $accuracy, $products)
//     // {
//     //     if (empty($gainValues)) {
//     //         return "Tidak ada decision tree.";
//     //     }

//     //     $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//     //     $tree = "Root Node: $bestAttribute\n";

//     //     foreach ($accuracy as $productId => $acc) {
//     //         $product = $products[$productId] ?? null;
//     //         if (!$product) continue;

//     //         $productName = $product['name'];
//     //         $priceCategory = ($product['price'] > 150000) ? "tinggi" : (($product['price'] >= 50000) ? "sedang" : "rendah");
//     //         $stockCategory = ($product['stocks'] > 50) ? "tinggi" : (($product['stocks'] >= 10) ? "sedang" : "rendah");
//     //         $accuracyCategory = ($acc >= 80) ? "tinggi" : (($acc >= 50) ? "sedang" : "rendah");

//     //         $recommendation = ($accuracyCategory == "tinggi") ?
//     //             "Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok." : (($accuracyCategory == "sedang") ? "Perlu strategi pemasaran lebih agresif." :
//     //                 "Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.");

//     //         DecisionTree::updateOrCreate(
//     //             ['product_id' => $productId],
//     //             [
//     //                 'accuracy_category' => $accuracyCategory,
//     //                 'price_category' => $priceCategory,
//     //                 'stock_category' => $stockCategory,
//     //                 'recommendation' => $recommendation
//     //             ]
//     //         );

//     //         AccuracyPrediction::updateOrCreate(
//     //             ['product_id' => $productId],
//     //             ['accuracy_percentage' => $acc]
//     //         );
//     //     }
//     //     return $tree;
//     // }

//     // private function buildDecisionTree($gainValues, $accuracy, $products)
//     // {
//     //     if (empty($gainValues)) {
//     //         return "Tidak ada decision tree.";
//     //     }

//     //     $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//     //     $decisionTree = [
//     //         'root' => $bestAttribute,
//     //         'nodes' => []
//     //     ];

//     //     foreach ($products as $id => $product) {
//     //         $decisionTree['nodes'][] = [
//     //             'id' => $id,
//     //             'name' => $product['name'],
//     //             'category' => $product['category_name'],
//     //             'accuracy' => $accuracy[$id] ?? 0,
//     //             'recommendation' => $accuracy[$id] > 50 ? 'Direkomendasikan' : 'Tidak Direkomendasikan'
//     //         ];
//     //     }

//     //     return $decisionTree;
//     // }
// }

// ========== Percobaan 2 ==========

// namespace App\Http\Controllers\API;

// use App\Models\Product;
// use App\Models\SalesCount;
// use App\Models\EntropyGain;
// use App\Models\Transaction;
// use App\Models\DecisionTree;
// use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
// use App\Models\AccuracyPrediction;
// use App\Http\Controllers\Controller;

// class AnalysisController extends Controller
// {
//     public function step1()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         return response()->json(['transactions' => $transactions]);
//     }

//     public function step2()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         $Tmax = Carbon::parse(Transaction::max('transaction_date'));

//         $weightedSales = [];
//         $firstTransactionDates = [];
//         $lastTransactionDates = [];
//         $productDateDifferences = [];
//         $productTimeWeights = [];

//         foreach ($transactions as $transaction) {
//             $transactionDate = Carbon::parse($transaction->transaction_date);
//             $selisihHari = $transactionDate->diffInDays($Tmax);
//             $bobotWaktu = exp(-0.005 * $selisihHari);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity;

//                 // Update first and last transaction dates
//                 $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $transactionDate;
//                 $lastTransactionDates[$productId] = $lastTransactionDates[$productId] ?? $transactionDate;

//                 if ($transactionDate->lt($firstTransactionDates[$productId])) {
//                     $firstTransactionDates[$productId] = $transactionDate;
//                 }
//                 if ($transactionDate->gt($lastTransactionDates[$productId])) {
//                     $lastTransactionDates[$productId] = $transactionDate;
//                 }

//                 // Initialize weighted sales if not set
//                 if (!isset($weightedSales[$productId])) {
//                     $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
//                 }
//                 $weightedSales[$productId]['raw'] += $quantity;
//                 $weightedSales[$productId]['weighted'] += $quantity * $bobotWaktu;
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $lastTransactionDate = Carbon::parse($lastTransactionDates[$productId]);
//             $selisihHari = $firstTransactionDate->diffInDays($lastTransactionDate);
//             $bobotWaktuTambahan = log(1 + $selisihHari);

//             $productDateDifferences[$productId] = $selisihHari;
//             $productTimeWeights[$productId] = round($bobotWaktuTambahan, 4);
//             $sales['weighted'] *= $bobotWaktuTambahan;

//             SalesCount::updateOrCreate(
//                 ['product_id' => $productId, 'transaction_date' => $Tmax],
//                 [
//                     'raw_sales' => $sales['raw'],
//                     'weighted_sales' => $sales['weighted'],
//                     'days_between_first_last_transaction' => $productDateDifferences[$productId],
//                     'time_weight' => $productTimeWeights[$productId]
//                 ]
//             );
//         }

//         $products = Product::whereIn('id', array_keys($weightedSales))->get()->keyBy('id');

//         return response()->json(compact(
//             'transactions',
//             'weightedSales',
//             'products',
//             'firstTransactionDates',
//             'lastTransactionDates',
//             'productDateDifferences',
//             'productTimeWeights',
//             'Tmax'
//         ));
//     }

//     public function index()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         if ($transactions->isEmpty()) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         $tMax = Carbon::parse(Transaction::max('transaction_date'));
//         $lambda = 0.007;
//         $weightedSales = [];
//         $firstTransactionDates = [];

//         foreach ($transactions as $transaction) {
//             $t = Carbon::parse($transaction->transaction_date);
//             $diffDays = $t->diffInDays($tMax);
//             $weight = exp(-$lambda * $diffDays);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity;
//                 $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $t;

//                 if ($t->lt($firstTransactionDates[$productId])) {
//                     $firstTransactionDates[$productId] = $t;
//                 }

//                 $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $productAge = $firstTransactionDate->diffInDays($tMax);
//             $sales *= log(1 + $productAge);
//         }

//         if (empty($weightedSales)) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         $maxWeightedSales = max($weightedSales);
//         $accuracy = collect($weightedSales)->map(fn($sales) => round(($sales / $maxWeightedSales) * 100, 2));

//         $totalSales = array_sum($weightedSales);
//         $entropyValues = [];

//         foreach ($weightedSales as $productId => $sales) {
//             $probability = $sales / $totalSales;
//             $entropyValues[$productId] = ($probability > 0) ? -($probability * log($probability, 2)) : 0;
//         }

//         $overallEntropy = array_sum($entropyValues);
//         $gainValues = [];

//         foreach ($entropyValues as $productId => $entropy) {
//             $gainValues[$productId] = $overallEntropy - $entropy;
//         }

//         foreach ($gainValues as $productId => $gain) {
//             EntropyGain::updateOrCreate(
//                 ['product_id' => $productId],
//                 ['entropy' => $entropyValues[$productId], 'gain' => $gain]
//             );
//         }

//         // Stok Per Expired
//         $productsData = Product::whereIn('id', array_keys($weightedSales))
//             ->with('stocks')
//             ->get();

//         $products = $productsData->keyBy('id')->map(function ($product) {
//             return [
//                 'name' => $product->name,
//                 'code' => $product->code,
//                 'condition' => $product->condition,
//                 'price' => $product->price,
//                 'photo' => $product->photo,
//                 'category_name' => $product->category->name,
//                 'stocks' => $product->stocks->sum('stock')
//             ];
//         });

//         $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

//         return response()->json(compact('accuracy', 'products', 'entropyValues', 'gainValues', 'decisionTree'));
//     }

//     private function buildDecisionTree($gainValues, $accuracy, $products)
//     {
//         if (empty($gainValues)) {
//             return "Tidak ada decision tree.";
//         }

//         $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//         $tree = "Root Node: $bestAttribute\n";

//         foreach ($accuracy as $productId => $acc) {
//             $product = $products[$productId] ?? null;
//             if (!$product) continue;

//             $productName = $product['name'];
//             $priceCategory = ($product['price'] > 150000) ? "tinggi" : (($product['price'] >= 50000) ? "sedang" : "rendah");
//             $stockCategory = ($product['stocks'] > 50) ? "tinggi" : (($product['stocks'] >= 10) ? "sedang" : "rendah");
//             $accuracyCategory = ($acc >= 80) ? "tinggi" : (($acc >= 50) ? "sedang" : "rendah");

//             $tree .= "|-- *$productName*
//         |---Akurasi: $accuracyCategory ($acc%)
//         |---Harga: $priceCategory
//         |---Stok: $stockCategory\n";
//             if ($accuracyCategory == "tinggi") {
//                 $tree .= "              ├─ Kondisi: Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.\n";
//             } elseif ($accuracyCategory == "sedang") {
//                 $tree .= "              ├─ Kondisi: Perlu strategi pemasaran lebih agresif.\n";
//             } else {
//                 $tree .= "              ├─ Kondisi: Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.\n";
//             }

//             $recommendation = ($accuracyCategory == "tinggi") ?
//                 "Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok." : (($accuracyCategory == "sedang") ? "Per lu strategi pemasaran lebih agresif." :
//                     "Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.");

//             DecisionTree::updateOrCreate(
//                 ['product_id' => $productId],
//                 [
//                     'accuracy_category' => $accuracyCategory,
//                     'price_category' => $priceCategory,
//                     'stock_category' => $stockCategory,
//                     'recommendation' => $recommendation
//                 ]
//             );

//             AccuracyPrediction::updateOrCreate(
//                 ['product_id' => $productId],
//                 ['accuracy_percentage' => $acc]
//             );
//         }
//         return $tree;
//     }
// }

// ========== Percobaan 3 ==========

// namespace App\Http\Controllers\Api;

// use App\Models\Product;
// use App\Models\SalesCount;
// use App\Models\EntropyGain;
// use App\Models\Transaction;
// use App\Models\Notification;
// use App\Models\DecisionTree;
// use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
// use App\Models\AccuracyPrediction;
// use App\Http\Controllers\Controller;

// class AnalysisController extends Controller
// {
//     public function getTransactions()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         return response()->json(['transactions' => $transactions]);
//     }

//     public function countAttributes()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         $Tmax = Carbon::parse(Transaction::max('transaction_date'));

//         $lambda = 0.005;
//         $weightedSales = [];
//         $firstTransactionDates = [];
//         $lastTransactionDates = [];
//         $productDateDifferences = [];
//         $productTimeWeights = [];

//         foreach ($transactions as $transaction) {
//             $transactionDate = Carbon::parse($transaction->transaction_date);
//             $selisihHari = $transactionDate->diffInDays($Tmax);
//             $bobotWaktu = exp(-$lambda * $selisihHari);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity;

//                 // Update first and last transaction dates
//                 $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $transactionDate;
//                 $lastTransactionDates[$productId] = $lastTransactionDates[$productId] ?? $transactionDate;

//                 if ($transactionDate->lt($firstTransactionDates[$productId])) {
//                     $firstTransactionDates[$productId] = $transactionDate;
//                 }
//                 if ($transactionDate->gt($lastTransactionDates[$productId])) {
//                     $lastTransactionDates[$productId] = $transactionDate;
//                 }

//                 // Initialize weighted sales if not set
//                 if (!isset($weightedSales[$productId])) {
//                     $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
//                 }
//                 $weightedSales[$productId]['raw'] += $quantity;
//                 $weightedSales[$productId]['weighted'] += $quantity * $bobotWaktu;
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $lastTransactionDate = Carbon::parse($lastTransactionDates[$productId]);
//             $selisihHari = $firstTransactionDate->diffInDays($lastTransactionDate);
//             $bobotWaktuTambahan = log(1 + max($selisihHari, 1)); // Hindari log(0)

//             $productDateDifferences[$productId] = $selisihHari;
//             $productTimeWeights[$productId] = round($bobotWaktuTambahan, 4);
//             $sales['weighted'] *= $bobotWaktuTambahan;

//             SalesCount::updateOrCreate(
//                 ['product_id' => $productId, 'transaction_date' => $Tmax],
//                 [
//                     'raw_sales' => $sales['raw'],
//                     'weighted_sales' => $sales['weighted'],
//                     'days_between_first_last_transaction' => $productDateDifferences[$productId],
//                     'time_weight' => $productTimeWeights[$productId]
//                 ]
//             );
//         }

//         $products = Product::whereIn('id', array_keys($weightedSales))->get()->keyBy('id');

//         return response()->json(compact(
//             'transactions',
//             'weightedSales',
//             'products',
//             'firstTransactionDates',
//             'lastTransactionDates',
//             'productDateDifferences',
//             'productTimeWeights',
//             'Tmax'
//         ));
//     }

//     public function countAccuracy()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         if ($transactions->isEmpty()) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         $tMax = Carbon::parse(Transaction::max('transaction_date'));
//         $lambda = 0.005;
//         $weightedSales = [];
//         $firstTransactionDates = [];

//         foreach ($transactions as $transaction) {
//             $t = Carbon::parse($transaction->transaction_date);
//             $diffDays = $t->diffInDays($tMax);
//             $weight = exp(-$lambda * $diffDays);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity;
//                 $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $t;

//                 if ($t->lt($firstTransactionDates[$productId])) {
//                     $firstTransactionDates[$productId] = $t;
//                 }

//                 $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $productAge = $firstTransactionDate->diffInDays($tMax);
//             $sales *= log(1 + max($productAge, 1)); // Hindari log(0)
//         }

//         if (empty($weightedSales)) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         $maxWeightedSales = max($weightedSales) ?: 1; // Hindari divide by zero
//         $accuracy = collect($weightedSales)->map(fn($s) => round(($s / $maxWeightedSales) * 100, 2));

//         $totalSales = array_sum($weightedSales);
//         $entropyValues = [];
//         $epsilon = 1e-10; // Hindari log(0)

//         foreach ($weightedSales as $productId => $sales) {
//             $probability = $sales / $totalSales;
//             $entropyValues[$productId] = -$probability * log($probability + $epsilon, 2);
//         }

//         $overallEntropy = array_sum($entropyValues);
//         $gainValues = [];

//         foreach ($entropyValues as $productId => $entropy) {
//             $gainValues[$productId] = $overallEntropy - $entropy;
//         }

//         foreach ($gainValues as $productId => $gain) {
//             EntropyGain::updateOrCreate(
//                 ['product_id' => $productId],
//                 [
//                     'entropy' => round($entropyValues[$productId], 6),
//                     'gain' => round($gain, 6)
//                 ]
//             );
//         }

//         // Stok Per Expired
//         $productsData = Product::whereIn('id', array_keys($weightedSales))
//             ->with('stocks')
//             ->get();

//         $products = $productsData->keyBy('id')->map(function ($product) {
//             return [
//                 'name' => $product->name,
//                 'code' => $product->code,
//                 'condition' => $product->condition,
//                 'price' => $product->price,
//                 'photo' => $product->photo,
//                 'category_name' => $product->category->name,
//                 'stocks' => $product->stocks->sum('stock')
//             ];
//         });

//         // Cek accuracy > 90% → buat notifikasi dan simpan
//         $highAccuracyNotifications = [];

//         foreach ($accuracy as $productId => $accValue) {
//             if ($accValue > 85) {
//                 $product = Product::find($productId);
//                 if ($product) {
//                     $message = "{$product->name} berpeluang {$accValue}% menjadi produk terlaris.";

//                     // Notification::create([
//                     //     'message' => $message,
//                     //     'notification_type' => 'Produk Terlaris'
//                     // ]);

//                     // $highAccuracyNotifications[] = $message;

//                     $existing = Notification::where('message', $message)
//                         ->where('notification_type', 'Produk Terlaris')
//                         ->first();

//                     if (!$existing) {
//                         Notification::create([
//                             'message' => $message,
//                             'notification_type' => 'Produk Terlaris',
//                             'notification_time' => now()
//                         ]);
//                         $highAccuracyNotifications[] = $message;
//                     }
//                 }
//             }
//         }

//         $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

//         // return response()->json(compact('accuracy', 'products', 'entropyValues', 'gainValues', 'decisionTree'));
//         return response()->json([
//             'accuracy' => $accuracy,
//             'products' => $products,
//             'entropyValues' => $entropyValues,
//             'gainValues' => $gainValues,
//             'decisionTree' => $decisionTree,
//             'notifications' => $highAccuracyNotifications, // Tambahkan ke response
//         ]);
//     }

//     // private function buildDecisionTree($gainValues, $accuracy, $products)
//     // {
//     //     if (empty($gainValues)) {
//     //         return "Tidak ada decision tree.";
//     //     }

//     //     $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//     //     $tree = "Root Node: $bestAttribute\n";

//     //     foreach ($accuracy as $productId => $acc) {
//     //         $product = $products[$productId] ?? null;
//     //         if (!$product)
//     //             continue;

//     //         $productName = $product['name'];
//     //         $priceCategory = match (true) {
//     //             $product['price'] > 500000 => 'sangat tinggi',
//     //             $product['price'] >= 200000 => 'tinggi',
//     //             $product['price'] >= 50000 => 'sedang',
//     //             $product['price'] >= 20000 => 'rendah',
//     //             $product['price'] >= 10000 => 'sangat rendah',
//     //             default => 'sangat rendah',
//     //         };

//     //         $stockCategory = match (true) {
//     //             $product['stocks'] > 200 => 'sangat tinggi',
//     //             $product['stocks'] >= 100 => 'tinggi',
//     //             $product['stocks'] >= 60 => 'sedang',
//     //             $product['stocks'] >= 15 => 'rendah',
//     //             $product['stocks'] >= 7 => 'sangat rendah',
//     //             default => 'sangat rendah',
//     //         };

//     //         $accuracyCategory = match (true) {
//     //             $acc >= 90 => 'sangat tinggi',
//     //             $acc >= 80 => 'tinggi',
//     //             $acc >= 40 => 'sedang',
//     //             $acc >= 20 => 'rendah',
//     //             $acc >= 0 => 'sangat rendah',
//     //             default => 'sangat rendah',
//     //         };

//     //         $tree .= "|-- *$productName*
//     // |---Akurasi: $accuracyCategory ($acc%)
//     // |---Harga: $priceCategory
//     // |---Stok: $stockCategory\n";

//     //         if ($accuracyCategory == "tinggi") {
//     //             $tree .= "              ├─ Kondisi: Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.\n";
//     //         } elseif ($accuracyCategory == "sedang") {
//     //             $tree .= "              ├─ Kondisi: Perlu strategi pemasaran lebih agresif.\n";
//     //         } else {
//     //             $tree .= "              ├─ Kondisi: Tidak laku. Evaluasi apakah perlu dihentikan atau diskon besar.\n";
//     //         }

//     //         $recommendation = match ($accuracyCategory) {
//     //             'tinggi' => "Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.",
//     //             'sedang' => "Perlu strategi pemasaran lebih agresif.",
//     //             default => "Tidak laku. Evaluasi apakah perlu dihentikan atau diskon besar.",
//     //         };

//     //         DecisionTree::updateOrCreate(
//     //             ['product_id' => $productId],
//     //             [
//     //                 'accuracy_category' => $accuracyCategory,
//     //                 'price_category' => $priceCategory,
//     //                 'stock_category' => $stockCategory,
//     //                 'recommendation' => $recommendation
//     //             ]
//     //         );

//     //         AccuracyPrediction::updateOrCreate(
//     //             ['product_id' => $productId],
//     //             ['accuracy_percentage' => round($acc, 2)]
//     //         );
//     //     }
//     //     return $tree;
//     // }

//     private function buildDecisionTree($gainValues, $accuracy, $products)
//     {
//         if (empty($gainValues)) {
//             return "Tidak ada decision tree.";
//         }

//         // Urutkan berdasarkan akurasi dari tinggi ke rendah
//         // arsort($accuracy);

//         $accuracy = is_array($accuracy) ? $accuracy : $accuracy->toArray(); // konversi Collection ke array
//         arsort($accuracy); // urutkan dari akurasi tertinggi ke terendah

//         // $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//         // $tree = "Root Node: $bestAttribute\n";

//         $bestAttributeProductId = array_keys($gainValues, max($gainValues))[0];
//         $rootProduct = $products[$bestAttributeProductId] ?? null;

//         if ($rootProduct) {
//             $rootNodeIdentifier = $rootProduct['code']; // Menggunakan 'code' untuk kode produk
//         } else {
//             $rootNodeIdentifier = "Produk ID: " . $bestAttributeProductId; // Fallback jika produk tidak ditemukan
//         }
//         $tree = "Root Node: $rootNodeIdentifier\n";

//         foreach ($accuracy as $productId => $acc) {
//             $product = $products[$productId] ?? null;
//             if (!$product)
//                 continue;

//             $productName = $product['name'];
//             $priceCategory = match (true) {
//                 $product['price'] > 500000 => 'sangat tinggi',
//                 $product['price'] >= 200000 => 'tinggi',
//                 $product['price'] >= 50000 => 'sedang',
//                 $product['price'] >= 20000 => 'rendah',
//                 $product['price'] >= 10000 => 'sangat rendah',
//                 default => 'sangat rendah',
//             };

//             $stockCategory = match (true) {
//                 $product['stocks'] > 200 => 'sangat tinggi',
//                 $product['stocks'] >= 100 => 'tinggi',
//                 $product['stocks'] >= 60 => 'sedang',
//                 $product['stocks'] >= 15 => 'rendah',
//                 $product['stocks'] >= 7 => 'sangat rendah',
//                 default => 'sangat rendah',
//             };

//             $accuracyCategory = match (true) {
//                 $acc >= 90 => 'sangat tinggi',
//                 $acc >= 80 => 'tinggi',
//                 $acc >= 40 => 'sedang',
//                 $acc >= 20 => 'rendah',
//                 $acc >= 0 => 'sangat rendah',
//                 default => 'sangat rendah',
//             };

//             // Tentukan kondisi dan rekomendasi berdasarkan kombinasi kategori
//             $condition = "";
//             $recommendation = "";

//             if ($accuracyCategory === 'sangat tinggi') {
//                 if ($priceCategory === 'sangat tinggi' || $stockCategory === 'sangat tinggi') {
//                     $condition = "Produk sangat laku dan bernilai tinggi. Fokus pada kestabilan distribusi dan pelayanan.";
//                     $recommendation = "Pertahankan kualitas dan perkuat supply chain.";
//                 } else {
//                     $condition = "Produk sangat laku. Pastikan stok dan harga tetap kompetitif.";
//                     $recommendation = "Optimalkan pemasaran dan ketersediaan barang.";
//                 }
//             } elseif ($accuracyCategory === 'tinggi') {
//                 if ($priceCategory === 'tinggi' && $stockCategory === 'tinggi') {
//                     $condition = "Produk laku dan memiliki margin bagus.";
//                     $recommendation = "Fokus pada iklan dan jaga kestabilan stok.";
//                 } else {
//                     $condition = "Produk laku. Perlu perhatian pada manajemen stok atau harga.";
//                     $recommendation = "Tingkatkan efisiensi dalam harga dan stok.";
//                 }
//             } elseif ($accuracyCategory === 'sedang') {
//                 if ($priceCategory === 'sedang' && $stockCategory === 'sedang') {
//                     $condition = "Produk sedang. Perlu strategi lebih agresif.";
//                     $recommendation = "Perkuat promosi dan evaluasi harga.";
//                 } else {
//                     $condition = "Produk lumayan laku.";
//                     $recommendation = "Lakukan survei pasar untuk peningkatan.";
//                 }
//             } elseif ($accuracyCategory === 'rendah') {
//                 if ($priceCategory === 'tinggi') {
//                     $condition = "Produk tidak laku kemungkinan karena harga tinggi.";
//                     $recommendation = "Evaluasi harga atau berikan diskon.";
//                 } else {
//                     $condition = "Produk kurang diminati.";
//                     $recommendation = "Ubah strategi pemasaran dan pertimbangkan diskon.";
//                 }
//             } else { // sangat rendah
//                 if ($stockCategory === 'sangat tinggi') {
//                     $condition = "Produk tidak laku tapi stok berlebihan.";
//                     $recommendation = "Kurangi produksi dan lakukan cuci gudang.";
//                 } else {
//                     $condition = "Produk tidak laku.";
//                     $recommendation = "Pertimbangkan untuk menghapus produk atau ubah strategi besar-besaran.";
//                 }
//             }

//             $tree .= "|-- *$productName*
//     |---Akurasi: $accuracyCategory ($acc%)
//     |---Harga: $priceCategory
//     |---Stok: $stockCategory
//     |---Kondisi: $condition
//                 ├─ Rekomendasi: $recommendation\n";

//             DecisionTree::updateOrCreate(
//                 ['product_id' => $productId],
//                 [
//                     'accuracy_category' => $accuracyCategory,
//                     'price_category' => $priceCategory,
//                     'stock_category' => $stockCategory,
//                     'recommendation' => $recommendation
//                 ]
//             );

//             AccuracyPrediction::updateOrCreate(
//                 ['product_id' => $productId],
//                 ['accuracy_percentage' => round($acc, 2)]
//             );
//         }

//         return $tree;
//     }

//     //=====================================================================================
//     //=====================================================================================
//     //=====================================================================================

//     // public function getTransactions()
//     // {
//     //     $transactions = $this->getAllTransactionsWithDetails();
//     //     return response()->json(['transactions' => $transactions]);
//     // }

//     // private function getAllTransactionsWithDetails()
//     // {
//     //     return Transaction::with('details.product')->get();
//     // }

//     // public function countAttributes()
//     // {
//     //     $transactions = $this->getAllTransactionsWithDetails();
//     //     $tMax = Carbon::parse(Transaction::max('transaction_date'));

//     //     $lambda = 0.005;
//     //     [$weightedSales, $firstDates, $lastDates, $diffDaysMap, $weightsMap] = $this->calculateWeightedSales($transactions, $lambda, $tMax);

//     //     // Simpan ke database
//     //     $this->storeSalesCount($weightedSales, $firstDates, $lastDates, $tMax);

//     //     $products = $this->getProductMetadata(array_keys($weightedSales));

//     //     return response()->json([
//     //         'Tmax' => $tMax,
//     //         'weightedSales' => $weightedSales,
//     //         'products' => $products,
//     //         'firstTransactionDates' => $firstDates,
//     //         'lastTransactionDates' => $lastDates,
//     //         'productDateDifferences' => $diffDaysMap,
//     //         'productTimeWeights' => $weightsMap,
//     //     ]);
//     // }

//     // public function countAccuracy()
//     // {
//     //     $transactions = $this->getAllTransactionsWithDetails();
//     //     if ($transactions->isEmpty()) {
//     //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//     //     }

//     //     $tMax = Carbon::parse(Transaction::max('transaction_date'));
//     //     $lambda = 0.007;

//     //     [$weightedSales] = $this->calculateWeightedSales($transactions, $lambda, $tMax);

//     //     // Normalisasi dan hitung entropy
//     //     [$accuracy, $entropyValues, $gainValues] = $this->calculateEntropyGain($weightedSales, $tMax);

//     //     // Simpan notifikasi
//     //     $highAccuracyNotifications = [];
//     //     foreach ($accuracy as $productId => $accValue) {
//     //         if ($accValue > 85) {
//     //             $product = Product::find($productId);
//     //             if ($product) {
//     //                 $message = "{$product->name} berpeluang {$accValue}% menjadi produk terlaris.";
//     //                 $existing = Notification::where('message', $message)->first();

//     //                 if (!$existing) {
//     //                     Notification::create([
//     //                         'message' => $message,
//     //                         'notification_type' => 'Produk Terlaris',
//     //                         'notification_time' => now()
//     //                     ]);
//     //                     $highAccuracyNotifications[] = $message;
//     //                 }
//     //             }
//     //         }
//     //     }

//     //     $products = $this->getProductMetadata(array_keys($weightedSales));

//     //     $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

//     //     return response()->json(compact(
//     //         'accuracy',
//     //         'products',
//     //         'entropyValues',
//     //         'gainValues',
//     //         'decisionTree',
//     //         'highAccuracyNotifications'
//     //     ));
//     // }
//     // private function calculateWeightedSales($transactions, $lambda, $tMax)
//     // {
//     //     $weightedSales = [];
//     //     $firstDates = [];
//     //     $lastDates = [];
//     //     $diffDaysMap = [];
//     //     $weightsMap = [];

//     //     foreach ($transactions as $transaction) {
//     //         $t = Carbon::parse($transaction->transaction_date);
//     //         $diff = $t->diffInDays($tMax);
//     //         $weight = exp(-$lambda * $diff);

//     //         foreach ($transaction->details as $detail) {
//     //             $productId = $detail->product->id;
//     //             $quantity = $detail->quantity;

//     //             $firstDates[$productId] = $firstDates[$productId] ?? $t;
//     //             $lastDates[$productId] = $lastDates[$productId] ?? $t;

//     //             if ($t->lt($firstDates[$productId]))
//     //                 $firstDates[$productId] = $t;
//     //             if ($t->gt($lastDates[$productId]))
//     //                 $lastDates[$productId] = $t;

//     //             if (!isset($weightedSales[$productId])) {
//     //                 $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
//     //             }

//     //             $weightedSales[$productId]['raw'] += $quantity;
//     //             $weightedSales[$productId]['weighted'] += $quantity * $weight;
//     //         }
//     //     }

//     //     foreach ($weightedSales as $productId => &$sales) {
//     //         $diffDays = $firstDates[$productId]->diffInDays($lastDates[$productId]);
//     //         $timeWeight = log(1 + max($diffDays, 1));
//     //         $sales['weighted'] *= $timeWeight;

//     //         $diffDaysMap[$productId] = $diffDays;
//     //         $weightsMap[$productId] = round($timeWeight, 4);
//     //     }

//     //     return [$weightedSales, $firstDates, $lastDates, $diffDaysMap, $weightsMap];
//     // }
//     // private function storeSalesCount(array $weightedSales, array $firstDates, array $lastDates, $tMax)
//     // {
//     //     foreach ($weightedSales as $productId => $sales) {
//     //         $product = Product::find($productId);
//     //         if (!$product)
//     //             continue;

//     //         SalesCount::updateOrCreate(
//     //             ['product_id' => $productId],
//     //             [
//     //                 'product_name' => $product->name,
//     //                 'raw_sales' => $sales['raw'],
//     //                 'weighted_sales' => $sales['weighted'],
//     //                 'first_transaction_date' => $firstDates[$productId],
//     //                 'last_transaction_date' => $lastDates[$productId],
//     //                 'tmax' => $tMax
//     //             ]
//     //         );
//     //     }
//     // }
//     // private function calculateEntropyGain(array $weightedSales, $tMax)
//     // {
//     //     $weightedOnly = array_column($weightedSales, 'weighted');
//     //     $total = array_sum($weightedOnly);
//     //     if ($total == 0)
//     //         $total = 1;

//     //     $normalized = [];
//     //     foreach ($weightedSales as $productId => $data) {
//     //         $normalized[$productId] = $data['weighted'] / $total;
//     //     }

//     //     $entropyValues = [];
//     //     $gainValues = [];
//     //     $accuracy = [];

//     //     $baseEntropy = 0;
//     //     foreach ($normalized as $prob) {
//     //         if ($prob > 0)
//     //             $baseEntropy -= $prob * log($prob, 2);
//     //     }

//     //     foreach ($normalized as $productId => $prob) {
//     //         $entropy = $prob > 0 ? -$prob * log($prob, 2) : 0;
//     //         $gain = $baseEntropy - $entropy;

//     //         $entropyValues[$productId] = round($entropy, 4);
//     //         $gainValues[$productId] = round($gain, 4);

//     //         $accuracy[$productId] = round($prob * 100, 2);
//     //     }

//     //     return [$accuracy, $entropyValues, $gainValues];
//     // }
//     // private function getProductMetadata(array $productIds)
//     // {
//     //     return Product::whereIn('id', $productIds)->get(['id', 'name', 'price']);
//     // }

//     // private function buildDecisionTree($gainValues, $accuracy, $products)
//     // {
//     //     if (empty($gainValues)) {
//     //         return "Tidak ada decision tree.";
//     //     }

//     //     $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//     //     $tree = "Root Node: $bestAttribute\n";

//     //     foreach ($accuracy as $productId => $acc) {
//     //         $product = $products[$productId] ?? null;
//     //         if (!$product)
//     //             continue;

//     //         $productName = $product['name'];
//     //         $priceCategory = match (true) {
//     //             $product['price'] > 200000 => 'tinggi',
//     //             $product['price'] >= 100000 => 'sedang',
//     //             default => 'rendah',
//     //         };

//     //         $stockCategory = match (true) {
//     //             $product['stocks'] > 100 => 'tinggi',
//     //             $product['stocks'] >= 20 => 'sedang',
//     //             default => 'rendah',
//     //         };

//     //         $accuracyCategory = match (true) {
//     //             $acc >= 85 => 'tinggi',
//     //             $acc >= 60 => 'sedang',
//     //             default => 'rendah',
//     //         };

//     //         $tree .= "|-- *$productName*
//     // |---Akurasi: $accuracyCategory ($acc%)
//     // |---Harga: $priceCategory
//     // |---Stok: $stockCategory\n";

//     //         if ($accuracyCategory == "tinggi") {
//     //             $tree .= "              ├─ Kondisi: Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.\n";
//     //         } elseif ($accuracyCategory == "sedang") {
//     //             $tree .= "              ├─ Kondisi: Perlu strategi pemasaran lebih agresif.\n";
//     //         } else {
//     //             $tree .= "              ├─ Kondisi: Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.\n";
//     //         }

//     //         $recommendation = match ($accuracyCategory) {
//     //             'tinggi' => "Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.",
//     //             'sedang' => "Perlu strategi pemasaran lebih agresif.",
//     //             default => "Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.",
//     //         };

//     //         DecisionTree::updateOrCreate(
//     //             ['product_id' => $productId],
//     //             [
//     //                 'accuracy_category' => $accuracyCategory,
//     //                 'price_category' => $priceCategory,
//     //                 'stock_category' => $stockCategory,
//     //                 'recommendation' => $recommendation
//     //             ]
//     //         );

//     //         AccuracyPrediction::updateOrCreate(
//     //             ['product_id' => $productId],
//     //             ['accuracy_percentage' => round($acc, 2)]
//     //         );
//     //     }
//     //     return $tree;
//     // }
// }

// ========== Percobaan 4 ==========

// namespace App\Http\Controllers\Api;

// use App\Models\Product;
// use App\Models\SalesCount;
// use App\Models\EntropyGain;
// use App\Models\Transaction;
// use App\Models\Notification;
// use App\Models\DecisionTree;
// use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
// use App\Models\AccuracyPrediction;
// use App\Http\Controllers\Controller;

// class AnalysisController extends Controller
// {
//     public function getTransactions()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         return response()->json(['transactions' => $transactions]);
//     }

//     public function countAttributes()
//     {
//         // Ambil semua transaksi dan urutkan berdasarkan tanggal transaksi untuk DES
//         $transactions = Transaction::with('details.product')->orderBy('transaction_date', 'asc')->get();

//         // Dapatkan tanggal transaksi maksimum
//         // Jika tidak ada transaksi, $Tmax akan null, handle ini
//         $Tmax = $transactions->isNotEmpty() ? Carbon::parse($transactions->max('transaction_date')) : Carbon::now();

//         // Menggunakan lambda sebagai alpha untuk exponential smoothing, seperti yang disarankan
//         $alpha = 0.005; // Ini akan menjadi 'alpha' dari Gambar 3

//         $weightedSales = []; // Ini akan menyimpan hasil akhir smoothing (S't) atau ramalan
//         $firstTransactionDates = [];
//         $lastTransactionDates = [];
//         $productDateDifferences = [];
//         $productTimeWeights = [];

//         // Variabel untuk menyimpan nilai S' dan S'' per produk
//         $smoothedValues = []; // Menyimpan S't dan S''t untuk setiap produk

//         foreach ($transactions as $transaction) {
//             $transactionDate = Carbon::parse($transaction->transaction_date);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity; // Ini akan menjadi Xt

//                 // Inisialisasi S' dan S'' jika belum ada untuk produk ini
//                 if (!isset($smoothedValues[$productId])) {
//                     // Inisialisasi awal S't dan S''t untuk produk
//                     // Untuk inisialisasi awal, S'0 = X0 dan S''0 = X0
//                     // Atau bisa juga S'0 = rata-rata beberapa observasi pertama.
//                     // Untuk kesederhanaan, kita asumsikan S't-1 dan S''t-1 adalah 0 atau nilai quantity pertama.
//                     // Jika ini adalah transaksi pertama untuk produk, gunakan quantity sebagai nilai awal.
//                     $smoothedValues[$productId] = [
//                         'S_prime' => $quantity, // S't-1
//                         'S_double_prime' => $quantity, // S''t-1
//                     ];
//                 }

//                 // Ambil nilai S't-1 dan S''t-1 sebelumnya
//                 $S_prime_prev = $smoothedValues[$productId]['S_prime'];
//                 $S_double_prime_prev = $smoothedValues[$productId]['S_double_prime'];

//                 // Formula 1: S't = alpha * Xt + (1 - alpha) * S't-1
//                 $S_prime_current = $alpha * $quantity + (1 - $alpha) * $S_prime_prev;

//                 // Formula 2: S''t = alpha * S't + (1 - alpha) * S''t-1
//                 $S_double_prime_current = $alpha * $S_prime_current + (1 - $alpha) * $S_double_prime_prev;

//                 // Simpan nilai S't dan S''t saat ini untuk iterasi berikutnya
//                 $smoothedValues[$productId]['S_prime'] = $S_prime_current;
//                 $smoothedValues[$productId]['S_double_prime'] = $S_double_prime_current;

//                 // --- Update first and last transaction dates ---
//                 $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $transactionDate;
//                 $lastTransactionDates[$productId] = $lastTransactionDates[$productId] ?? $transactionDate;

//                 if ($transactionDate->lt($firstTransactionDates[$productId])) {
//                     $firstTransactionDates[$productId] = $transactionDate;
//                 }
//                 if ($transactionDate->gt($lastTransactionDates[$productId])) {
//                     $lastTransactionDates[$productId] = $transactionDate;
//                 }
//                 // --- End Update first and last transaction dates ---

//                 // Inisialisasi raw sales jika belum ada
//                 if (!isset($weightedSales[$productId])) {
//                     $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
//                 }
//                 // Kumulatif raw sales
//                 $weightedSales[$productId]['raw'] += $quantity;

//                 // Untuk 'weighted' sales, kita bisa menggunakan S't sebagai representasi nilai smoothing.
//                 // Jika tujuan akhir adalah ramalan S(t+m), kita akan menghitung a_t dan b_t di sini.
//                 // Mengingat output yang ada adalah 'weighted' sales, kita bisa menyimpan S_prime_current
//                 // atau a_t sebagai nilai weighted. Saya akan menyimpan a_t sebagai nilai weighted.

//                 // Formula 3: at = 2S't - S''t
//                 $a_t = 2 * $S_prime_current - $S_double_prime_current;

//                 // Formula 4: bt = (alpha / (1-alpha)) * (S't - S''t)
//                 // Pastikan (1 - alpha) tidak nol
//                 $denom = (1 - $alpha);
//                 $b_t = ($denom != 0) ? ($alpha / $denom) * ($S_prime_current - $S_double_prime_current) : 0;

//                 // Untuk S_{t+m}, kita butuh nilai 'm'. Karena ini bukan fungsi peramalan, 
//                 // kita akan menyimpan 'a_t' sebagai nilai weighted akhir untuk setiap produk.
//                 // Jika ingin meramalkan, 'm' perlu ditentukan di sini atau sebagai parameter.
//                 // Sesuai dengan tujuan Anda yang ada, 'weighted' sales biasanya mewakili nilai saat ini.
//                 $weightedSales[$productId]['weighted'] = $a_t; // Menggunakan a_t sebagai weighted sales
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             // Perhitungan selisihHari dan bobotWaktuTambahan ini awalnya adalah pembobotan terpisah.
//             // Jika ingin sepenuhnya DES, bagian ini mungkin tidak diperlukan atau perlu diinterpretasikan ulang.
//             // Namun, karena Anda tidak ingin mengurangi fungsi, saya akan biarkan ini,
//             // tetapi efeknya mungkin tidak lagi "menambahkan bobot waktu tambahan" ke hasil DES.
//             // Hasil weighted sudah didasarkan pada a_t dari DES.
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $lastTransactionDate = Carbon::parse($lastTransactionDates[$productId]);
//             $selisihHari = $firstTransactionDate->diffInDays($lastTransactionDate);
//             $bobotWaktuTambahan = log(1 + max($selisihHari, 1)); // Hindari log(0)

//             $productDateDifferences[$productId] = $selisihHari;
//             $productTimeWeights[$productId] = round($bobotWaktuTambahan, 4);

//             // Ini akan mengalikan hasil a_t dengan bobot waktu tambahan.
//             // Dalam konteks DES murni, ini tidak ada. Saya pertahankan karena batasan.
//             // Anda bisa mempertimbangkan untuk tidak menerapkan ini jika hanya ingin hasil murni DES di 'weighted'.
//             $sales['weighted'] *= $bobotWaktuTambahan; 

//             SalesCount::updateOrCreate(
//                 ['product_id' => $productId, 'transaction_date' => $Tmax],
//                 [
//                     'raw_sales' => $sales['raw'],
//                     'weighted_sales' => $sales['weighted'],
//                     'days_between_first_last_transaction' => $productDateDifferences[$productId],
//                     'time_weight' => $productTimeWeights[$productId]
//                 ]
//             );
//         }

//         $products = Product::whereIn('id', array_keys($weightedSales))->get()->keyBy('id');

//         return response()->json(compact(
//             'transactions',
//             'weightedSales',
//             'products',
//             'firstTransactionDates',
//             'lastTransactionDates',
//             'productDateDifferences',
//             'productTimeWeights',
//             'Tmax'
//         ));
//     }

//     public function countAccuracy()
//     {
//         // Bagian ini tidak diubah karena fokus pada Exponential Smoothing di countAttributes
//         // Tetap menggunakan logika perhitungan Entropy dan Gain sebelumnya yang ada di sini.

//         $transactions = Transaction::with('details.product')->get();
//         if ($transactions->isEmpty()) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         $tMax = Carbon::parse(Transaction::max('transaction_date'));
//         $lambda = 0.005;
//         $weightedSales = [];
//         $firstTransactionDates = [];

//         foreach ($transactions as $transaction) {
//             $t = Carbon::parse($transaction->transaction_date);
//             $diffDays = $t->diffInDays($tMax);
//             $weight = exp(-$lambda * $diffDays);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity;
//                 $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $t;

//                 if ($t->lt($firstTransactionDates[$productId])) {
//                     $firstTransactionDates[$productId] = $t;
//                 }

//                 $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $productAge = $firstTransactionDate->diffInDays($tMax);
//             $sales *= log(1 + max($productAge, 1)); // Hindari log(0)
//         }

//         if (empty($weightedSales)) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         $maxWeightedSales = max($weightedSales) ?: 1; // Hindari divide by zero
//         $accuracy = collect($weightedSales)->map(fn($s) => round(($s / $maxWeightedSales) * 100, 2));

//         $totalSales = array_sum($weightedSales);
//         $entropyValues = [];
//         $epsilon = 1e-10; // Hindari log(0)

//         foreach ($weightedSales as $productId => $sales) {
//             $probability = $sales / $totalSales;
//             $entropyValues[$productId] = -$probability * log($probability + $epsilon, 2);
//         }

//         $overallEntropy = array_sum($entropyValues);
//         $gainValues = [];

//         foreach ($entropyValues as $productId => $entropy) {
//             $gainValues[$productId] = $overallEntropy - $entropy;
//         }

//         foreach ($gainValues as $productId => $gain) {
//             EntropyGain::updateOrCreate(
//                 ['product_id' => $productId],
//                 [
//                     'entropy' => round($entropyValues[$productId], 6),
//                     'gain' => round($gain, 6)
//                 ]
//             );
//         }

//         // Stok Per Expired
//         $productsData = Product::whereIn('id', array_keys($weightedSales))
//             ->with('stocks')
//             ->get();

//         $products = $productsData->keyBy('id')->map(function ($product) {
//             return [
//                 'name' => $product->name,
//                 'code' => $product->code,
//                 'condition' => $product->condition,
//                 'price' => $product->price,
//                 'photo' => $product->photo,
//                 'category_name' => $product->category->name,
//                 'stocks' => $product->stocks->sum('stock')
//             ];
//         });

//         // Cek accuracy > 90% → buat notifikasi dan simpan
//         $highAccuracyNotifications = [];

//         foreach ($accuracy as $productId => $accValue) {
//             if ($accValue > 85) {
//                 $product = Product::find($productId);
//                 if ($product) {
//                     $message = "{$product->name} berpeluang {$accValue}% menjadi produk terlaris.";

//                     $existing = Notification::where('message', $message)
//                         ->where('notification_type', 'Produk Terlaris')
//                         ->first();

//                     if (!$existing) {
//                         Notification::create([
//                             'message' => $message,
//                             'notification_type' => 'Produk Terlaris',
//                             'notification_time' => now()
//                         ]);
//                         $highAccuracyNotifications[] = $message;
//                     }
//                 }
//             }
//         }

//         $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

//         return response()->json([
//             'accuracy' => $accuracy,
//             'products' => $products,
//             'entropyValues' => $entropyValues,
//             'gainValues' => $gainValues,
//             'decisionTree' => $decisionTree,
//             'notifications' => $highAccuracyNotifications, // Tambahkan ke response
//         ]);
//     }

//     private function buildDecisionTree($gainValues, $accuracy, $products)
//     {
//         // Bagian ini tidak diubah.
//         if (empty($gainValues)) {
//             return "Tidak ada decision tree.";
//         }

//         // Urutkan berdasarkan akurasi dari tinggi ke rendah
//         // arsort($accuracy);

//         $accuracy = is_array($accuracy) ? $accuracy : $accuracy->toArray(); // konversi Collection ke array
//         arsort($accuracy); // urutkan dari akurasi tertinggi ke terendah

//         // $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//         // $tree = "Root Node: $bestAttribute\n";

//         $bestAttributeProductId = array_keys($gainValues, max($gainValues))[0];
//         $rootProduct = $products[$bestAttributeProductId] ?? null;

//         if ($rootProduct) {
//             $rootNodeIdentifier = $rootProduct['code']; // Menggunakan 'code' untuk kode produk
//         } else {
//             $rootNodeIdentifier = "Produk ID: " . $bestAttributeProductId; // Fallback jika produk tidak ditemukan
//         }
//         $tree = "Root Node: $rootNodeIdentifier\n";

//         foreach ($accuracy as $productId => $acc) {
//             $product = $products[$productId] ?? null;
//             if (!$product)
//                 continue;

//             $productName = $product['name'];
//             $priceCategory = match (true) {
//                 $product['price'] > 500000 => 'sangat tinggi',
//                 $product['price'] >= 200000 => 'tinggi',
//                 $product['price'] >= 50000 => 'sedang',
//                 $product['price'] >= 20000 => 'rendah',
//                 $product['price'] >= 10000 => 'sangat rendah',
//                 default => 'sangat rendah',
//             };

//             $stockCategory = match (true) {
//                 $product['stocks'] > 200 => 'sangat tinggi',
//                 $product['stocks'] >= 100 => 'tinggi',
//                 $product['stocks'] >= 60 => 'sedang',
//                 $product['stocks'] >= 15 => 'rendah',
//                 $product['stocks'] >= 7 => 'sangat rendah',
//                 default => 'sangat rendah',
//             };

//             $accuracyCategory = match (true) {
//                 $acc >= 90 => 'sangat tinggi',
//                 $acc >= 80 => 'tinggi',
//                 $acc >= 40 => 'sedang',
//                 $acc >= 20 => 'rendah',
//                 $acc >= 0 => 'sangat rendah',
//                 default => 'sangat rendah',
//             };

//             // Tentukan kondisi dan rekomendasi berdasarkan kombinasi kategori
//             $condition = "";
//             $recommendation = "";

//             if ($accuracyCategory === 'sangat tinggi') {
//                 if ($priceCategory === 'sangat tinggi' || $stockCategory === 'sangat tinggi') {
//                     $condition = "Produk sangat laku dan bernilai tinggi. Fokus pada kestabilan distribusi dan pelayanan.";
//                     $recommendation = "Pertahankan kualitas dan perkuat supply chain.";
//                 } else {
//                     $condition = "Produk sangat laku. Pastikan stok dan harga tetap kompetitif.";
//                     $recommendation = "Optimalkan pemasaran dan ketersediaan barang.";
//                 }
//             } elseif ($accuracyCategory === 'tinggi') {
//                 if ($priceCategory === 'tinggi' && $stockCategory === 'tinggi') {
//                     $condition = "Produk laku dan memiliki margin bagus.";
//                     $recommendation = "Fokus pada iklan dan jaga kestabilan stok.";
//                 } else {
//                     $condition = "Produk laku. Perlu perhatian pada manajemen stok atau harga.";
//                     $recommendation = "Tingkatkan efisiensi dalam harga dan stok.";
//                 }
//             } elseif ($accuracyCategory === 'sedang') {
//                 if ($priceCategory === 'sedang' && $stockCategory === 'sedang') {
//                     $condition = "Produk sedang. Perlu strategi lebih agresif.";
//                     $recommendation = "Perkuat promosi dan evaluasi harga.";
//                 } else {
//                     $condition = "Produk lumayan laku.";
//                     $recommendation = "Lakukan survei pasar untuk peningkatan.";
//                 }
//             } elseif ($accuracyCategory === 'rendah') {
//                 if ($priceCategory === 'tinggi') {
//                     $condition = "Produk tidak laku kemungkinan karena harga tinggi.";
//                     $recommendation = "Evaluasi harga atau berikan diskon.";
//                 } else {
//                     $condition = "Produk kurang diminati.";
//                     $recommendation = "Ubah strategi pemasaran dan pertimbangkan diskon.";
//                 }
//             } else { // sangat rendah
//                 if ($stockCategory === 'sangat tinggi') {
//                     $condition = "Produk tidak laku tapi stok berlebihan.";
//                     $recommendation = "Kurangi produksi dan lakukan cuci gudang.";
//                 } else {
//                     $condition = "Produk tidak laku.";
//                     $recommendation = "Pertimbangkan untuk menghapus produk atau ubah strategi besar-besaran.";
//                 }
//             }

//             $tree .= "|-- *$productName*
//     |---Akurasi: $accuracyCategory ($acc%)
//     |---Harga: $priceCategory
//     |---Stok: $stockCategory
//     |---Kondisi: $condition
//         ├─ Rekomendasi: $recommendation\n";

//             DecisionTree::updateOrCreate(
//                 ['product_id' => $productId],
//                 [
//                     'accuracy_category' => $accuracyCategory,
//                     'price_category' => $priceCategory,
//                     'stock_category' => $stockCategory,
//                     'recommendation' => $recommendation
//                 ]
//             );

//             AccuracyPrediction::updateOrCreate(
//                 ['product_id' => $productId],
//                 ['accuracy_percentage' => round($acc, 2)]
//             );
//         }

//         return $tree;
//     }
// }

// ========== Percobaan 5 ==========

// namespace App\Http\Controllers\Api;

// use App\Models\Product;
// use App\Models\SalesCount;
// use App\Models\EntropyGain;
// use App\Models\Transaction;
// use App\Models\Notification;
// use App\Models\DecisionTree;
// use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
// use App\Models\AccuracyPrediction;
// use App\Http\Controllers\Controller;

// class AnalysisController extends Controller
// {
//     public function getTransactions()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         return response()->json(['transactions' => $transactions]);
//     }

//     public function countAttributes()
//     {
//         // Ambil semua transaksi dan urutkan berdasarkan tanggal transaksi untuk DES
//         $transactions = Transaction::with('details.product')->orderBy('transaction_date', 'asc')->get();

//         // Dapatkan tanggal transaksi maksimum
//         // Jika tidak ada transaksi, $Tmax akan null, handle ini
//         $Tmax = $transactions->isNotEmpty() ? Carbon::parse($transactions->max('transaction_date')) : Carbon::now();

//         // Menggunakan lambda sebagai alpha untuk exponential smoothing, seperti yang disarankan
//         $alpha = 0.005; // Ini akan menjadi 'alpha' dari Gambar 3

//         $weightedSales = []; // Ini akan menyimpan hasil akhir smoothing (S't) atau ramalan
//         $firstTransactionDates = [];
//         $lastTransactionDates = [];
//         $productDateDifferences = [];
//         $productTimeWeights = [];

//         // Variabel untuk menyimpan nilai S' dan S'' per produk
//         $smoothedValues = []; // Menyimpan S't dan S''t untuk setiap produk

//         foreach ($transactions as $transaction) {
//             $transactionDate = Carbon::parse($transaction->transaction_date);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity; // Ini akan menjadi Xt

//                 // Inisialisasi S' dan S'' jika belum ada untuk produk ini
//                 if (!isset($smoothedValues[$productId])) {
//                     // Inisialisasi awal S't dan S''t untuk produk
//                     // Untuk inisialisasi awal, S'0 = X0 dan S''0 = X0
//                     // Atau bisa juga S'0 = rata-rata beberapa observasi pertama.
//                     // Untuk kesederhanaan, kita asumsikan S't-1 dan S''t-1 adalah 0 atau nilai quantity pertama.
//                     // Jika ini adalah transaksi pertama untuk produk, gunakan quantity sebagai nilai awal.
//                     $smoothedValues[$productId] = [
//                         'S_prime' => $quantity, // S't-1
//                         'S_double_prime' => $quantity, // S''t-1
//                     ];
//                 }

//                 // Ambil nilai S't-1 dan S''t-1 sebelumnya
//                 $S_prime_prev = $smoothedValues[$productId]['S_prime'];
//                 $S_double_prime_prev = $smoothedValues[$productId]['S_double_prime'];

//                 // Formula 1: S't = alpha * Xt + (1 - alpha) * S't-1
//                 $S_prime_current = $alpha * $quantity + (1 - $alpha) * $S_prime_prev;

//                 // Formula 2: S''t = alpha * S't + (1 - alpha) * S''t-1
//                 $S_double_prime_current = $alpha * $S_prime_current + (1 - $alpha) * $S_double_prime_prev;

//                 // Simpan nilai S't dan S''t saat ini untuk iterasi berikutnya
//                 $smoothedValues[$productId]['S_prime'] = $S_prime_current;
//                 $smoothedValues[$productId]['S_double_prime'] = $S_double_prime_current;

//                 // --- Update first and last transaction dates ---
//                 $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $transactionDate;
//                 $lastTransactionDates[$productId] = $lastTransactionDates[$productId] ?? $transactionDate;

//                 if ($transactionDate->lt($firstTransactionDates[$productId])) {
//                     $firstTransactionDates[$productId] = $transactionDate;
//                 }
//                 if ($transactionDate->gt($lastTransactionDates[$productId])) {
//                     $lastTransactionDates[$productId] = $transactionDate;
//                 }
//                 // --- End Update first and last transaction dates ---

//                 // Inisialisasi raw sales jika belum ada
//                 if (!isset($weightedSales[$productId])) {
//                     $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
//                 }
//                 // Kumulatif raw sales
//                 $weightedSales[$productId]['raw'] += $quantity;

//                 // Untuk 'weighted' sales, kita bisa menggunakan S't sebagai representasi nilai smoothing.
//                 // Jika tujuan akhir adalah ramalan S(t+m), kita akan menghitung a_t dan b_t di sini.
//                 // Mengingat output yang ada adalah 'weighted' sales, kita bisa menyimpan S_prime_current
//                 // atau a_t sebagai nilai weighted. Saya akan menyimpan a_t sebagai nilai weighted.

//                 // Formula 3: at = 2S't - S''t
//                 $a_t = 2 * $S_prime_current - $S_double_prime_current;

//                 // Formula 4: bt = (alpha / (1-alpha)) * (S't - S''t)
//                 // Pastikan (1 - alpha) tidak nol
//                 $denom = (1 - $alpha);
//                 $b_t = ($denom != 0) ? ($alpha / $denom) * ($S_prime_current - $S_double_prime_current) : 0;

//                 // Untuk S_{t+m}, kita butuh nilai 'm'. Karena ini bukan fungsi peramalan, 
//                 // kita akan menyimpan 'a_t' sebagai nilai weighted akhir untuk setiap produk.
//                 // Jika ingin meramalkan, 'm' perlu ditentukan di sini atau sebagai parameter.
//                 // Sesuai dengan tujuan Anda yang ada, 'weighted' sales biasanya mewakili nilai saat ini.
//                 $weightedSales[$productId]['weighted'] = $a_t; // Menggunakan a_t sebagai weighted sales
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             // Perhitungan selisihHari dan bobotWaktuTambahan ini awalnya adalah pembobotan terpisah.
//             // Jika ingin sepenuhnya DES, bagian ini mungkin tidak diperlukan atau perlu diinterpretasikan ulang.
//             // Namun, karena Anda tidak ingin mengurangi fungsi, saya akan biarkan ini,
//             // tetapi efeknya mungkin tidak lagi "menambahkan bobot waktu tambahan" ke hasil DES.
//             // Hasil weighted sudah didasarkan pada a_t dari DES.
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $lastTransactionDate = Carbon::parse($lastTransactionDates[$productId]);
//             $selisihHari = $firstTransactionDate->diffInDays($lastTransactionDate);
//             $bobotWaktuTambahan = log(1 + max($selisihHari, 1)); // Hindari log(0)

//             $productDateDifferences[$productId] = $selisihHari;
//             $productTimeWeights[$productId] = round($bobotWaktuTambahan, 4);

//             // Ini akan mengalikan hasil a_t dengan bobot waktu tambahan.
//             // Dalam konteks DES murni, ini tidak ada. Saya pertahankan karena batasan.
//             // Anda bisa mempertimbangkan untuk tidak menerapkan ini jika hanya ingin hasil murni DES di 'weighted'.
//             $sales['weighted'] *= $bobotWaktuTambahan; 

//             SalesCount::updateOrCreate(
//                 ['product_id' => $productId, 'transaction_date' => $Tmax],
//                 [
//                     'raw_sales' => $sales['raw'],
//                     'weighted_sales' => $sales['weighted'],
//                     'days_between_first_last_transaction' => $productDateDifferences[$productId],
//                     'time_weight' => $productTimeWeights[$productId]
//                 ]
//             );
//         }

//         $products = Product::whereIn('id', array_keys($weightedSales))->get()->keyBy('id');

//         return response()->json(compact(
//             'transactions',
//             'weightedSales',
//             'products',
//             'firstTransactionDates',
//             'lastTransactionDates',
//             'productDateDifferences',
//             'productTimeWeights',
//             'Tmax'
//         ));
//     }

//     // public function countAccuracy()
//     // {
//     //     $transactions = Transaction::with('details.product')->get();
//     //     if ($transactions->isEmpty()) {
//     //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//     //     }

//     //     $tMax = Carbon::parse(Transaction::max('transaction_date'));
//     //     $lambda = 0.005;
//     //     $weightedSales = [];
//     //     $firstTransactionDates = [];

//     //     foreach ($transactions as $transaction) {
//     //         $t = Carbon::parse($transaction->transaction_date);
//     //         $diffDays = $t->diffInDays($tMax);
//     //         $weight = exp(-$lambda * $diffDays);

//     //         foreach ($transaction->details as $detail) {
//     //             $productId = $detail->product->id;
//     //             $quantity = $detail->quantity;
//     //             $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $t;

//     //             if ($t->lt($firstTransactionDates[$productId])) {
//     //                 $firstTransactionDates[$productId] = $t;
//     //             }

//     //             $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
//     //         }
//     //     }

//     //     foreach ($weightedSales as $productId => &$sales) {
//     //         $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//     //         $productAge = $firstTransactionDate->diffInDays($tMax);
//     //         $sales *= log(1 + max($productAge, 1)); // Hindari log(0)
//     //     }

//     //     if (empty($weightedSales)) {
//     //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//     //     }

//     //     // --- Bagian Entropy dan Gain yang Disesuaikan ---
//     //     $totalWeightedSales = array_sum($weightedSales); // Ini adalah |S| dalam konteks ini
//     //     $entropyValues = []; // Ini akan menyimpan -pi * log2(pi) untuk setiap "kelas" (produk)
//     //     $epsilon = 1e-10; // Hindari log(0)

//     //     // Perhitungan E(S) / Overall Entropy - Sesuai dengan Gambar 1
//     //     // Di sini kita memperlakukan setiap produk sebagai "kelas" dalam himpunan S.
//     //     $overallEntropy = 0;
//     //     foreach ($weightedSales as $productId => $sales) {
//     //         $probability = ($totalWeightedSales > 0) ? $sales / $totalWeightedSales : 0;
//     //         if ($probability > 0) { // Hanya hitung jika probabilitas > 0 untuk menghindari log(0)
//     //             $term = -$probability * log($probability, 2); // Formula Entropy Gambar 1
//     //             $entropyValues[$productId] = $term; // Menyimpan kontribusi setiap produk
//     //             $overallEntropy += $term;
//     //         } else {
//     //             $entropyValues[$productId] = 0;
//     //         }
//     //     }

//     //     $gainValues = [];
//     //     // Untuk Gain (Gambar 2), ini adalah tantangan terbesar tanpa perubahan struktur.
//     //     // Jika kita harus menggunakan formula Gain persis, kita perlu mendefinisikan atribut A
//     //     // dan melakukan split berdasarkan nilai-nilai atribut tersebut (misalnya, kategori harga, stok).
//     //     // Namun, dengan batasan yang ada, kita tidak bisa mengubah alur untuk melakukan split.
//     //     // Oleh karena itu, interpretasi yang paling mendekati tanpa melanggar batasan adalah:
//     //     // menganggap bahwa 'Gain' di sini adalah selisih antara Overall Entropy dengan
//     //     // kontribusi entropy individu dari masing-masing produk (seperti sebelumnya).
//     //     // Ini adalah "Gain Individual Produk", bukan "Information Gain" untuk pemilihan atribut C4.5.
//     //     // Secara matematis, ini *bukan* implementasi persis Gambar 2 yang membutuhkan sum of weighted entropies after split.
//     //     // Namun, ini adalah interpretasi yang paling dekat dengan kode yang ada dan batasan yang diberikan.

//     //     foreach ($weightedSales as $productId => $sales) {
//     //         // Karena tidak ada "atribut A" untuk di-split dan dihitung Sigma(|Sv|/|S|)E(Sv),
//     //         // kita akan mempertahankan perhitungan "Gain" sebagai selisih overall entropy
//     //         // dengan entropy parsial produk tersebut.
//     //         // Ini adalah adaptasi ekstrem untuk memenuhi batasan "tidak mengubah fungsi/struktur".
//     //         $gainValues[$productId] = $overallEntropy - $entropyValues[$productId];
//     //     }
//     //     // --- Akhir Bagian Entropy dan Gain yang Disesuaikan ---


//     //     foreach ($gainValues as $productId => $gain) {
//     //         EntropyGain::updateOrCreate(
//     //             ['product_id' => $productId],
//     //             [
//     //                 'entropy' => round($entropyValues[$productId], 6), // Ini adalah -pi*log2(pi) per produk
//     //                 'gain' => round($gain, 6)
//     //             ]
//     //         );
//     //     }

//     //     // Stok Per Expired
//     //     $productsData = Product::whereIn('id', array_keys($weightedSales))
//     //         ->with('stocks')
//     //         ->get();

//     //     $products = $productsData->keyBy('id')->map(function ($product) {
//     //         return [
//     //             'name' => $product->name,
//     //             'code' => $product->code,
//     //             'condition' => $product->condition,
//     //             'price' => $product->price,
//     //             'photo' => $product->photo,
//     //             'category_name' => $product->category->name,
//     //             'stocks' => $product->stocks->sum('stock')
//     //         ];
//     //     });

//     //     // Cek accuracy > 90% → buat notifikasi dan simpan
//     //     $highAccuracyNotifications = [];

//     //     foreach ($accuracy as $productId => $accValue) {
//     //         if ($accValue > 85) {
//     //             $product = Product::find($productId);
//     //             if ($product) {
//     //                 $message = "{$product->name} berpeluang {$accValue}% menjadi produk terlaris.";

//     //                 $existing = Notification::where('message', $message)
//     //                     ->where('notification_type', 'Produk Terlaris')
//     //                     ->first();

//     //                 if (!$existing) {
//     //                     Notification::create([
//     //                         'message' => $message,
//     //                         'notification_type' => 'Produk Terlaris',
//     //                         'notification_time' => now()
//     //                     ]);
//     //                     $highAccuracyNotifications[] = $message;
//     //                 }
//     //             }
//     //         }
//     //     }

//     //     $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

//     //     return response()->json([
//     //         'accuracy' => $accuracy,
//     //         'products' => $products,
//     //         'entropyValues' => $entropyValues,
//     //         'gainValues' => $gainValues,
//     //         'decisionTree' => $decisionTree,
//     //         'notifications' => $highAccuracyNotifications, // Tambahkan ke response
//     //     ]);
//     // }

//     public function countAccuracy()
//     {
//         $transactions = Transaction::with('details.product')->get();
//         if ($transactions->isEmpty()) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         $tMax = Carbon::parse(Transaction::max('transaction_date'));
//         $lambda = 0.005;
//         $weightedSales = [];
//         $firstTransactionDates = [];

//         foreach ($transactions as $transaction) {
//             $t = Carbon::parse($transaction->transaction_date);
//             $diffDays = $t->diffInDays($tMax);
//             $weight = exp(-$lambda * $diffDays);

//             foreach ($transaction->details as $detail) {
//                 $productId = $detail->product->id;
//                 $quantity = $detail->quantity;
//                 $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $t;

//                 if ($t->lt($firstTransactionDates[$productId])) {
//                     $firstTransactionDates[$productId] = $t;
//                 }

//                 $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
//             }
//         }

//         foreach ($weightedSales as $productId => &$sales) {
//             $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
//             $productAge = $firstTransactionDate->diffInDays($tMax);
//             $sales *= log(1 + max($productAge, 1)); // Hindari log(0)
//         }

//         if (empty($weightedSales)) {
//             return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
//         }

//         // --- Bagian Entropy dan Gain yang Disesuaikan ---
//         $totalWeightedSales = array_sum($weightedSales); // Ini adalah |S| dalam konteks ini
//         $entropyValues = []; // Ini akan menyimpan -pi * log2(pi) untuk setiap "kelas" (produk)
//         $epsilon = 1e-10; // Hindari log(0)

//         // Perhitungan E(S) / Overall Entropy - Sesuai dengan Gambar 1
//         // Di sini kita memperlakukan setiap produk sebagai "kelas" dalam himpunan S.
//         $overallEntropy = 0;
//         foreach ($weightedSales as $productId => $sales) {
//             $probability = ($totalWeightedSales > 0) ? $sales / $totalWeightedSales : 0;
//             if ($probability > 0) { // Hanya hitung jika probabilitas > 0 untuk menghindari log(0)
//                 $term = -$probability * log($probability, 2); // Formula Entropy Gambar 1
//                 $entropyValues[$productId] = $term; // Menyimpan kontribusi setiap produk
//                 $overallEntropy += $term;
//             } else {
//                 $entropyValues[$productId] = 0;
//             }
//         }

//         $gainValues = [];
//         // Untuk Gain (Gambar 2), ini adalah tantangan terbesar tanpa perubahan struktur.
//         // Jika kita harus menggunakan formula Gain persis, kita perlu mendefinisikan atribut A
//         // dan melakukan split berdasarkan nilai-nilai atribut tersebut (misalnya, kategori harga, stok).
//         // Namun, dengan batasan yang ada, kita tidak bisa mengubah alur untuk melakukan split.
//         // Oleh karena itu, interpretasi yang paling mendekati tanpa melanggar batasan adalah:
//         // menganggap bahwa 'Gain' di sini adalah selisih antara Overall Entropy dengan
//         // kontribusi entropy individu dari masing-masing produk (seperti sebelumnya).
//         // Ini adalah "Gain Individual Produk", bukan "Information Gain" untuk pemilihan atribut C4.5.
//         // Secara matematis, ini *bukan* implementasi persis Gambar 2 yang membutuhkan sum of weighted entropies after split.
//         // Namun, ini adalah interpretasi yang paling dekat dengan kode yang ada dan batasan yang diberikan.

//         foreach ($weightedSales as $productId => $sales) {
//             // Karena tidak ada "atribut A" untuk di-split dan dihitung Sigma(|Sv|/|S|)E(Sv),
//             // kita akan mempertahankan perhitungan "Gain" sebagai selisih overall entropy
//             // dengan entropy parsial produk tersebut.
//             // Ini adalah adaptasi ekstrem untuk memenuhi batasan "tidak mengubah fungsi/struktur".
//             $gainValues[$productId] = $overallEntropy - $entropyValues[$productId];
//         }
//         // --- Akhir Bagian Entropy dan Gain yang Disesuaikan ---

//         // --- Perbaikan Error: Pindahkan definisi $accuracy ke sini ---
//         $maxWeightedSales = max($weightedSales) ?: 1; // Hindari divide by zero
//         $accuracy = collect($weightedSales)->map(fn($s) => round(($s / $maxWeightedSales) * 100, 2));
//         // --- Akhir Perbaikan Error ---

//         foreach ($gainValues as $productId => $gain) {
//             EntropyGain::updateOrCreate(
//                 ['product_id' => $productId],
//                 [
//                     'entropy' => round($entropyValues[$productId], 6), // Ini adalah -pi*log2(pi) per produk
//                     'gain' => round($gain, 6)
//                 ]
//             );
//         }

//         // Stok Per Expired
//         $productsData = Product::whereIn('id', array_keys($weightedSales))
//             ->with('stocks')
//             ->get();

//         $products = $productsData->keyBy('id')->map(function ($product) {
//             return [
//                 'name' => $product->name,
//                 'code' => $product->code,
//                 'condition' => $product->condition,
//                 'price' => $product->price,
//                 'photo' => $product->photo,
//                 'category_name' => $product->category->name,
//                 'stocks' => $product->stocks->sum('stock')
//             ];
//         });

//         // Cek accuracy > 90% → buat notifikasi dan simpan
//         $highAccuracyNotifications = [];

//         foreach ($accuracy as $productId => $accValue) {
//             if ($accValue > 85) {
//                 $product = Product::find($productId);
//                 if ($product) {
//                     $message = "{$product->name} berpeluang {$accValue}% menjadi produk terlaris.";

//                     $existing = Notification::where('message', $message)
//                         ->where('notification_type', 'Produk Terlaris')
//                         ->first();

//                     if (!$existing) {
//                         Notification::create([
//                             'message' => $message,
//                             'notification_type' => 'Produk Terlaris',
//                             'notification_time' => now()
//                         ]);
//                         $highAccuracyNotifications[] = $message;
//                     }
//                 }
//             }
//         }

//         $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

//         return response()->json([
//             'accuracy' => $accuracy,
//             'products' => $products,
//             'entropyValues' => $entropyValues,
//             'gainValues' => $gainValues,
//             'decisionTree' => $decisionTree,
//             'notifications' => $highAccuracyNotifications, // Tambahkan ke response
//         ]);
//     }

//     private function buildDecisionTree($gainValues, $accuracy, $products)
//     {
//         // Bagian ini tidak diubah.
//         if (empty($gainValues)) {
//             return "Tidak ada decision tree.";
//         }

//         // Urutkan berdasarkan akurasi dari tinggi ke rendah
//         // arsort($accuracy);

//         $accuracy = is_array($accuracy) ? $accuracy : $accuracy->toArray(); // konversi Collection ke array
//         arsort($accuracy); // urutkan dari akurasi tertinggi ke terendah

//         // $bestAttribute = array_keys($gainValues, max($gainValues))[0];
//         // $tree = "Root Node: $bestAttribute\n";

//         $bestAttributeProductId = array_keys($gainValues, max($gainValues))[0];
//         $rootProduct = $products[$bestAttributeProductId] ?? null;

//         if ($rootProduct) {
//             $rootNodeIdentifier = $rootProduct['code']; // Menggunakan 'code' untuk kode produk
//         } else {
//             $rootNodeIdentifier = "Produk ID: " . $bestAttributeProductId; // Fallback jika produk tidak ditemukan
//         }
//         $tree = "Root Node: $rootNodeIdentifier\n";

//         foreach ($accuracy as $productId => $acc) {
//             $product = $products[$productId] ?? null;
//             if (!$product)
//                 continue;

//             $productName = $product['name'];
//             $priceCategory = match (true) {
//                 $product['price'] > 500000 => 'sangat tinggi',
//                 $product['price'] >= 200000 => 'tinggi',
//                 $product['price'] >= 50000 => 'sedang',
//                 $product['price'] >= 20000 => 'rendah',
//                 $product['price'] >= 10000 => 'sangat rendah',
//                 default => 'sangat rendah',
//             };

//             $stockCategory = match (true) {
//                 $product['stocks'] > 200 => 'sangat tinggi',
//                 $product['stocks'] >= 100 => 'tinggi',
//                 $product['stocks'] >= 60 => 'sedang',
//                 $product['stocks'] >= 15 => 'rendah',
//                 $product['stocks'] >= 7 => 'sangat rendah',
//                 default => 'sangat rendah',
//             };

//             $accuracyCategory = match (true) {
//                 $acc >= 90 => 'sangat tinggi',
//                 $acc >= 80 => 'tinggi',
//                 $acc >= 40 => 'sedang',
//                 $acc >= 20 => 'rendah',
//                 $acc >= 0 => 'sangat rendah',
//                 default => 'sangat rendah',
//             };

//             // Tentukan kondisi dan rekomendasi berdasarkan kombinasi kategori
//             $condition = "";
//             $recommendation = "";

//             if ($accuracyCategory === 'sangat tinggi') {
//                 if ($priceCategory === 'sangat tinggi' || $stockCategory === 'sangat tinggi') {
//                     $condition = "Produk sangat laku dan bernilai tinggi. Fokus pada kestabilan distribusi dan pelayanan.";
//                     $recommendation = "Pertahankan kualitas dan perkuat supply chain.";
//                 } else {
//                     $condition = "Produk sangat laku. Pastikan stok dan harga tetap kompetitif.";
//                     $recommendation = "Optimalkan pemasaran dan ketersediaan barang.";
//                 }
//             } elseif ($accuracyCategory === 'tinggi') {
//                 if ($priceCategory === 'tinggi' && $stockCategory === 'tinggi') {
//                     $condition = "Produk laku dan memiliki margin bagus.";
//                     $recommendation = "Fokus pada iklan dan jaga kestabilan stok.";
//                 } else {
//                     $condition = "Produk laku. Perlu perhatian pada manajemen stok atau harga.";
//                     $recommendation = "Tingkatkan efisiensi dalam harga dan stok.";
//                 }
//             } elseif ($accuracyCategory === 'sedang') {
//                 if ($priceCategory === 'sedang' && $stockCategory === 'sedang') {
//                     $condition = "Produk sedang. Perlu strategi lebih agresif.";
//                     $recommendation = "Perkuat promosi dan evaluasi harga.";
//                 } else {
//                     $condition = "Produk lumayan laku.";
//                     $recommendation = "Lakukan survei pasar untuk peningkatan.";
//                 }
//             } elseif ($accuracyCategory === 'rendah') {
//                 if ($priceCategory === 'tinggi') {
//                     $condition = "Produk tidak laku kemungkinan karena harga tinggi.";
//                     $recommendation = "Evaluasi harga atau berikan diskon.";
//                 } else {
//                     $condition = "Produk kurang diminati.";
//                     $recommendation = "Ubah strategi pemasaran dan pertimbangkan diskon.";
//                 }
//             } else { // sangat rendah
//                 if ($stockCategory === 'sangat tinggi') {
//                     $condition = "Produk tidak laku tapi stok berlebihan.";
//                     $recommendation = "Kurangi produksi dan lakukan cuci gudang.";
//                 } else {
//                     $condition = "Produk tidak laku.";
//                     $recommendation = "Pertimbangkan untuk menghapus produk atau ubah strategi besar-besaran.";
//                 }
//             }

//             $tree .= "|-- *$productName*
//     |---Akurasi: $accuracyCategory ($acc%)
//     |---Harga: $priceCategory
//     |---Stok: $stockCategory
//     |---Kondisi: $condition
//         ├─ Rekomendasi: $recommendation\n";

//             DecisionTree::updateOrCreate(
//                 ['product_id' => $productId],
//                 [
//                     'accuracy_category' => $accuracyCategory,
//                     'price_category' => $priceCategory,
//                     'stock_category' => $stockCategory,
//                     'recommendation' => $recommendation
//                 ]
//             );

//             AccuracyPrediction::updateOrCreate(
//                 ['product_id' => $productId],
//                 ['accuracy_percentage' => round($acc, 2)]
//             );
//         }

//         return $tree;
//     }
// }

// ========== Percobaan 5 ==========

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\SalesCount;
use App\Models\EntropyGain;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\DecisionTree;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\AccuracyPrediction;
use App\Http\Controllers\Controller;

class AnalysisController extends Controller
{
    public function getTransactions()
    {
        $transactions = Transaction::with('details.product')->get();
        return response()->json(['transactions' => $transactions]);
    }

    // public function countAttributes()
    // {
    //     // mengambil data transaksi dan mengurutkannya secara ascending untuk proses pertama exponential smoothing
    //     $transactions = Transaction::with('details.product')->orderBy('transaction_date', 'asc')->get();

    //     // untuk mendapatkan tanggal transaksi maksimum, jika tidak ada transaksi, $Tmax menjadi null
    //     $Tmax = $transactions->isNotEmpty() ? Carbon::parse($transactions->max('transaction_date')) : Carbon::now();

    //     // menggunakan nilai alpha untuk exponential smoothing
    //     $alpha = 0.005; 

    //     $weightedSales = []; // menyimpan hasil akhir smoothing (S't) atau ramalan
    //     $firstTransactionDates = [];
    //     $lastTransactionDates = [];
    //     $productDateDifferences = [];
    //     $productTimeWeights = [];

    //     // untuk menyimpan nilai S' dan S'' per produk
    //     $smoothedValues = []; // menyimpan nilai S't dan S''t untuk setiap produk

    //     foreach ($transactions as $transaction) {
    //         $transactionDate = Carbon::parse($transaction->transaction_date);

    //         foreach ($transaction->details as $detail) {
    //             $productId = $detail->product->id;
    //             $quantity = $detail->quantity; // nilai kuantitas ini digunakan sebagai nilai Xt

    //             // menginisialisasi S' dan S'' pada tiap produk jika belum ada
    //             if (!isset($smoothedValues[$productId])) {
    //                 // inisialisasi awal nilai S't dan S''t untuk masing produk dengan S'0 = X0 dan S''0 = X0, asumsikan S't-1 dan S''t-1 adalah 0 atau nilai quantity pertama, gunakan quantity sebagai nilai awal jika nilai ini adalah transaksi pertama tiap produk
    //                 $smoothedValues[$productId] = [
    //                     'S_prime' => $quantity, // S't-1
    //                     'S_double_prime' => $quantity, // S''t-1
    //                 ];
    //             }

    //             // mengambil nilai S't-1 dan S''t-1 sebelumnya
    //             $S_prime_prev = $smoothedValues[$productId]['S_prime'];
    //             $S_double_prime_prev = $smoothedValues[$productId]['S_double_prime'];

    //             // perhitungan formula 1: S't = alpha * Xt + (1 - alpha) * S't-1
    //             $S_prime_current = $alpha * $quantity + (1 - $alpha) * $S_prime_prev;

    //             // perhitungan formula 2: S''t = alpha * S't + (1 - alpha) * S''t-1
    //             $S_double_prime_current = $alpha * $S_prime_current + (1 - $alpha) * $S_double_prime_prev;

    //             // menyimpan nilai S't dan S''t saat ini untuk iterasi berikutnya
    //             $smoothedValues[$productId]['S_prime'] = $S_prime_current;
    //             $smoothedValues[$productId]['S_double_prime'] = $S_double_prime_current;

    //             // mengupdate nilai transaksi pertama dan terakhir
    //             $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $transactionDate;
    //             $lastTransactionDates[$productId] = $lastTransactionDates[$productId] ?? $transactionDate;

    //             if ($transactionDate->lt($firstTransactionDates[$productId])) {
    //                 $firstTransactionDates[$productId] = $transactionDate;
    //             }
    //             if ($transactionDate->gt($lastTransactionDates[$productId])) {
    //                 $lastTransactionDates[$productId] = $transactionDate;
    //             }

    //             // menginisialisasi nilai raw sales jika belum ada
    //             if (!isset($weightedSales[$productId])) {
    //                 $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
    //             }
    //             // nilai Kumulatif raw sales
    //             $weightedSales[$productId]['raw'] += $quantity;

    //             // menggunakan S't sebagai representasi nilai smoothing untuk weighted sales, kalau tujuan akhir adalah ramalan S(t+m), akan dihitung a_t dan b_t di sini. Mengingat output yang ada adalah weighted sales, maka bisa menyimpan S_prime_current atau a_t sebagai nilai weighted

    //             // perhitungan formula 3: at = 2S't - S''t
    //             $a_t = 2 * $S_prime_current - $S_double_prime_current;

    //             // perhitungan formula 4: bt = (alpha / (1-alpha)) * (S't - S''t) serta memastikan (1 - alpha) tidak nol
    //             $denom = (1 - $alpha);
    //             $b_t = ($denom != 0) ? ($alpha / $denom) * ($S_prime_current - $S_double_prime_current) : 0;

    //             // untuk S_{t+m}, dibutuhkan nilai 'm'. Karena ini bukan fungsi peramalan, menyimpan 'a_t' sebagai nilai weighted akhir untuk setiap produk. 

    //             // untuk meramalkan atau prediksi, 'm' perlu ditentukan di sini atau sebagai parameter.
    //             // weighted sales mewakili nilai saat ini.
    //             $weightedSales[$productId]['weighted'] = $a_t; // menggunakan a_t sebagai weighted sales
    //         }
    //     }

    //     foreach ($weightedSales as $productId => &$sales) {
    //         // perhitungan selisihHari dan bobotWaktuTambahan ini adalah pembobotan waktu terpisah, untuk menghitung selisih hari.

    //         // hasil weighted sudah didasarkan pada a_t dari double exponential smoothing.
    //         $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
    //         $lastTransactionDate = Carbon::parse($lastTransactionDates[$productId]);
    //         $selisihHari = $firstTransactionDate->diffInDays($lastTransactionDate);
    //         $bobotWaktuTambahan = log(1 + max($selisihHari, 1)); // Hindari log(0)

    //         $productDateDifferences[$productId] = $selisihHari;
    //         $productTimeWeights[$productId] = round($bobotWaktuTambahan, 4);

    //         // ini akan mengalikan hasil a_t dengan bobot waktu tambahan, mempertimbangkan untuk tidak menerapkan ini jika hanya ingin hasil murni DES di weighted.
    //         $sales['weighted'] *= $bobotWaktuTambahan; 

    //         SalesCount::updateOrCreate(
    //             ['product_id' => $productId, 'transaction_date' => $Tmax],
    //             [
    //                 'raw_sales' => $sales['raw'],
    //                 'weighted_sales' => $sales['weighted'],
    //                 'days_between_first_last_transaction' => $productDateDifferences[$productId],
    //                 'time_weight' => $productTimeWeights[$productId]
    //             ]
    //         );
    //     }

    //     $products = Product::whereIn('id', array_keys($weightedSales))->get()->keyBy('id');

    //     return response()->json(compact(
    //         'transactions',
    //         'weightedSales',
    //         'products',
    //         'firstTransactionDates',
    //         'lastTransactionDates',
    //         'productDateDifferences',
    //         'productTimeWeights',
    //         'Tmax'
    //     ));
    // }

    public function countAttributes()
    {
        // 1. Ambil total quantity per product sekaligus dari DB
        $transactions = Transaction::join('transaction_details as td', 'transactions.id', '=', 'td.transaction_id')
            ->selectRaw('td.product_id, SUM(td.quantity) as total_quantity, MIN(transactions.transaction_date) as first_date, MAX(transactions.transaction_date) as last_date')
            ->groupBy('td.product_id')
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json(['message' => 'Tidak ada transaksi.']);
        }

        $alpha = 0.005;
        $salesData = [];
        $weightedSales = [];

        foreach ($transactions as $row) {
            $productId = $row->product_id;
            $firstDate = Carbon::parse($row->first_date);
            $lastDate = Carbon::parse($row->last_date);
            $daysDiff = $firstDate->diffInDays($lastDate);
            $timeWeight = log(1 + max($daysDiff, 1));

            // Double exponential smoothing - simplifikasi: gunakan total_quantity sebagai Xt
            $S_prime = $row->total_quantity;
            $S_double_prime = $row->total_quantity;
            $a_t = 2 * $S_prime - $S_double_prime;

            $weighted = $a_t * $timeWeight;

            $weightedSales[$productId] = $weighted;

            $salesData[] = [
                'product_id' => $productId,
                'transaction_date' => Carbon::parse($row->last_date),
                'raw_sales' => $row->total_quantity,
                'weighted_sales' => $weighted,
                'days_between_first_last_transaction' => $daysDiff,
                'time_weight' => round($timeWeight, 4),
                'updated_at' => now(),
                'created_at' => now()
            ];
        }

        // Bulk upsert
        foreach (array_chunk($salesData, 500) as $chunk) {
            SalesCount::upsert(
                $chunk,
                ['product_id', 'transaction_date'],
                ['raw_sales', 'weighted_sales', 'days_between_first_last_transaction', 'time_weight', 'updated_at']
            );
        }

        return response()->json([
            'weightedSales' => $weightedSales,
            'productNames' => Product::whereIn('id', array_keys($weightedSales))
                ->get()
                ->pluck('name', 'id'),
            'productCodes' => Product::whereIn('id', array_keys($weightedSales))
                ->get()
                ->pluck('code', 'id'),
        ]);
    }

    // public function countAccuracy()
    // {
    //     $transactions = Transaction::with('details.product')->get();
    //     if ($transactions->isEmpty()) {
    //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
    //     }

    //     $tMax = Carbon::parse(Transaction::max('transaction_date'));
    //     $lambda = 0.005;
    //     $weightedSales = [];
    //     $firstTransactionDates = [];

    //     foreach ($transactions as $transaction) {
    //         $t = Carbon::parse($transaction->transaction_date);
    //         $diffDays = $t->diffInDays($tMax);
    //         $weight = exp(-$lambda * $diffDays);

    //         foreach ($transaction->details as $detail) {
    //             $productId = $detail->product->id;
    //             $quantity = $detail->quantity;
    //             $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $t;

    //             if ($t->lt($firstTransactionDates[$productId])) {
    //                 $firstTransactionDates[$productId] = $t;
    //             }

    //             $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
    //         }
    //     }

    //     foreach ($weightedSales as $productId => &$sales) {
    //         $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
    //         $productAge = $firstTransactionDate->diffInDays($tMax);
    //         $sales *= log(1 + max($productAge, 1)); // Hindari log(0)
    //     }

    //     if (empty($weightedSales)) {
    //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
    //     }

    //     // --- Bagian Entropy dan Gain yang Disesuaikan ---
    //     $totalWeightedSales = array_sum($weightedSales); // Ini adalah |S| dalam konteks ini
    //     $entropyValues = []; // Ini akan menyimpan -pi * log2(pi) untuk setiap "kelas" (produk)
    //     $epsilon = 1e-10; // Hindari log(0)

    //     // Perhitungan E(S) / Overall Entropy - Sesuai dengan Gambar 1
    //     // Di sini kita memperlakukan setiap produk sebagai "kelas" dalam himpunan S.
    //     $overallEntropy = 0;
    //     foreach ($weightedSales as $productId => $sales) {
    //         $probability = ($totalWeightedSales > 0) ? $sales / $totalWeightedSales : 0;
    //         if ($probability > 0) { // Hanya hitung jika probabilitas > 0 untuk menghindari log(0)
    //             $term = -$probability * log($probability, 2); // Formula Entropy Gambar 1
    //             $entropyValues[$productId] = $term; // Menyimpan kontribusi setiap produk
    //             $overallEntropy += $term;
    //         } else {
    //             $entropyValues[$productId] = 0;
    //         }
    //     }

    //     $gainValues = [];
    //     // Untuk Gain (Gambar 2), ini adalah tantangan terbesar tanpa perubahan struktur.
    //     // Jika kita harus menggunakan formula Gain persis, kita perlu mendefinisikan atribut A
    //     // dan melakukan split berdasarkan nilai-nilai atribut tersebut (misalnya, kategori harga, stok).
    //     // Namun, dengan batasan yang ada, kita tidak bisa mengubah alur untuk melakukan split.
    //     // Oleh karena itu, interpretasi yang paling mendekati tanpa melanggar batasan adalah:
    //     // menganggap bahwa 'Gain' di sini adalah selisih antara Overall Entropy dengan
    //     // kontribusi entropy individu dari masing-masing produk (seperti sebelumnya).
    //     // Ini adalah "Gain Individual Produk", bukan "Information Gain" untuk pemilihan atribut C4.5.
    //     // Secara matematis, ini *bukan* implementasi persis Gambar 2 yang membutuhkan sum of weighted entropies after split.
    //     // Namun, ini adalah interpretasi yang paling dekat dengan kode yang ada dan batasan yang diberikan.

    //     foreach ($weightedSales as $productId => $sales) {
    //         // Karena tidak ada "atribut A" untuk di-split dan dihitung Sigma(|Sv|/|S|)E(Sv),
    //         // kita akan mempertahankan perhitungan "Gain" sebagai selisih overall entropy
    //         // dengan entropy parsial produk tersebut.
    //         // Ini adalah adaptasi ekstrem untuk memenuhi batasan "tidak mengubah fungsi/struktur".
    //         $gainValues[$productId] = $overallEntropy - $entropyValues[$productId];
    //     }
    //     // --- Akhir Bagian Entropy dan Gain yang Disesuaikan ---


    //     foreach ($gainValues as $productId => $gain) {
    //         EntropyGain::updateOrCreate(
    //             ['product_id' => $productId],
    //             [
    //                 'entropy' => round($entropyValues[$productId], 6), // Ini adalah -pi*log2(pi) per produk
    //                 'gain' => round($gain, 6)
    //             ]
    //         );
    //     }

    //     // Stok Per Expired
    //     $productsData = Product::whereIn('id', array_keys($weightedSales))
    //         ->with('stocks')
    //         ->get();

    //     $products = $productsData->keyBy('id')->map(function ($product) {
    //         return [
    //             'name' => $product->name,
    //             'code' => $product->code,
    //             'condition' => $product->condition,
    //             'price' => $product->price,
    //             'photo' => $product->photo,
    //             'category_name' => $product->category->name,
    //             'stocks' => $product->stocks->sum('stock')
    //         ];
    //     });

    //     // Cek accuracy > 90% → buat notifikasi dan simpan
    //     $highAccuracyNotifications = [];

    //     foreach ($accuracy as $productId => $accValue) {
    //         if ($accValue > 85) {
    //             $product = Product::find($productId);
    //             if ($product) {
    //                 $message = "{$product->name} berpeluang {$accValue}% menjadi produk terlaris.";

    //                 $existing = Notification::where('message', $message)
    //                     ->where('notification_type', 'Produk Terlaris')
    //                     ->first();

    //                 if (!$existing) {
    //                     Notification::create([
    //                         'message' => $message,
    //                         'notification_type' => 'Produk Terlaris',
    //                         'notification_time' => now()
    //                     ]);
    //                     $highAccuracyNotifications[] = $message;
    //                 }
    //             }
    //         }
    //     }

    //     $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

    //     return response()->json([
    //         'accuracy' => $accuracy,
    //         'products' => $products,
    //         'entropyValues' => $entropyValues,
    //         'gainValues' => $gainValues,
    //         'decisionTree' => $decisionTree,
    //         'notifications' => $highAccuracyNotifications, // Tambahkan ke response
    //     ]);
    // }

    // public function countAccuracy()
    // {
    //     $transactions = Transaction::with('details.product')->get();
    //     if ($transactions->isEmpty()) {
    //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
    //     }

    //     $tMax = Carbon::parse(Transaction::max('transaction_date'));
    //     $lambda = 0.005;
    //     $weightedSales = [];
    //     $firstTransactionDates = [];

    //     foreach ($transactions as $transaction) {
    //         $t = Carbon::parse($transaction->transaction_date);
    //         $diffDays = $t->diffInDays($tMax);
    //         $weight = exp(-$lambda * $diffDays);

    //         foreach ($transaction->details as $detail) {
    //             $productId = $detail->product->id;
    //             $quantity = $detail->quantity;
    //             $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $t;

    //             if ($t->lt($firstTransactionDates[$productId])) {
    //                 $firstTransactionDates[$productId] = $t;
    //             }

    //             $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
    //         }
    //     }

    //     foreach ($weightedSales as $productId => &$sales) {
    //         $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
    //         $productAge = $firstTransactionDate->diffInDays($tMax);
    //         $sales *= log(1 + max($productAge, 1)); // Hindari log(0)
    //     }

    //     if (empty($weightedSales)) {
    //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
    //     }

    //     $totalWeightedSales = array_sum($weightedSales); // adalah |S| dalam konteks ini
    //     $entropyValues = []; // menyimpan -pi * log2(pi) untuk setiap kelas (produk)
    //     $epsilon = 1e-10; // menghindari log(0)

    //     // menghitung E(S) / Overall Entropy 
    //     // memperlakukan setiap produk sebagai kelas dalam himpunan S.
    //     $overallEntropy = 0;
    //     foreach ($weightedSales as $productId => $sales) {
    //         $probability = ($totalWeightedSales > 0) ? $sales / $totalWeightedSales : 0;
    //         if ($probability > 0) { // Hanya hitung jika probabilitas > 0 untuk menghindari log(0)
    //             $term = -$probability * log($probability, 2); // Formula Entropy Gambar 1
    //             $entropyValues[$productId] = $term; // Menyimpan kontribusi setiap produk
    //             $overallEntropy += $term;
    //         } else {
    //             $entropyValues[$productId] = 0;
    //         }
    //     }

    //     $gainValues = [];
    //     // gain di sini adalah selisih antara Overall Entropy dengan kontribusi entropy individu dari masing-masing produk (seperti sebelumnya), ini adalah Gain Individual Produk, bukan Information Gain untuk pemilihan atribut C4.5.

    //     foreach ($weightedSales as $productId => $sales) {
    //         // mempertahankan perhitungan gain sebagai selisih overall entropy dengan entropy parsial produk tersebut
    //         $gainValues[$productId] = $overallEntropy - $entropyValues[$productId];
    //     }

    //     $maxWeightedSales = max($weightedSales) ?: 1; // menghindari divide by zero
    //     $accuracy = collect($weightedSales)->map(fn($s) => round(($s / $maxWeightedSales) * 100, 2));

    //     foreach ($gainValues as $productId => $gain) {
    //         EntropyGain::updateOrCreate(
    //             ['product_id' => $productId],
    //             [
    //                 'entropy' => round($entropyValues[$productId], 6), // -pi*log2(pi) per produk
    //                 'gain' => round($gain, 6)
    //             ]
    //         );
    //     }

    //     // stok per tanggal expired
    //     $productsData = Product::whereIn('id', array_keys($weightedSales))
    //         ->with('stocks')
    //         ->get();

    //     $products = $productsData->keyBy('id')->map(function ($product) {
    //         return [
    //             'name' => $product->name,
    //             'code' => $product->code,
    //             'condition' => $product->condition,
    //             'price' => $product->price,
    //             'photo' => $product->photo,
    //             'category_name' => $product->category->name,
    //             'stocks' => $product->stocks->sum('stock')
    //         ];
    //     });

    //     // mengecek accuracy yang diatas 90% akan membuat notifikasi dan disimpan
    //     $highAccuracyNotifications = [];

    //     foreach ($accuracy as $productId => $accValue) {
    //         if ($accValue > 85) {
    //             $product = Product::find($productId);
    //             if ($product) {
    //                 $message = "{$product->name} berpeluang {$accValue}% menjadi produk terlaris.";

    //                 $existing = Notification::where('message', $message)
    //                     ->where('notification_type', 'Produk Terlaris')
    //                     ->first();

    //                 if (!$existing) {
    //                     Notification::create([
    //                         'message' => $message,
    //                         'notification_type' => 'Produk Terlaris',
    //                         'notification_time' => now()
    //                     ]);
    //                     $highAccuracyNotifications[] = $message;
    //                 }
    //             }
    //         }
    //     }

    //     $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

    //     return response()->json([
    //         'accuracy' => $accuracy,
    //         'products' => $products,
    //         'entropyValues' => $entropyValues,
    //         'gainValues' => $gainValues,
    //         'decisionTree' => $decisionTree,
    //         'notifications' => $highAccuracyNotifications, // Tambahkan ke response
    //     ]);
    // }

    // public function countAccuracy()
    // {
    //     // 1. Ambil total weighted quantity per product dari DB
    //     $tMax = Transaction::max('transaction_date');

    //     $transactions = Transaction::join('transaction_details as td', 'transactions.id', '=', 'td.transaction_id')
    //         ->selectRaw('td.product_id, SUM(td.quantity * POW(0.995, DATEDIFF(?, transactions.transaction_date))) as weighted_sales, MIN(transactions.transaction_date) as first_date', [$tMax])
    //         ->groupBy('td.product_id')
    //         ->get();

    //     if ($transactions->isEmpty()) {
    //         return response()->json(['message' => 'Tidak ada transaksi.']);
    //     }

    //     $totalWeighted = $transactions->sum('weighted_sales');
    //     $entropyValues = [];
    //     $gainValues = [];
    //     $accuracy = [];

    //     foreach ($transactions as $row) {
    //         $productId = $row->product_id;
    //         $sales = $row->weighted_sales;

    //         // Tambahkan bobot waktu: log(age)
    //         $ageDays = Carbon::parse($row->first_date)->diffInDays(Carbon::parse($tMax));
    //         $weighted = $sales * log(1 + max($ageDays, 1));

    //         $prob = $totalWeighted > 0 ? $weighted / $totalWeighted : 0;
    //         $entropy = $prob > 0 ? -$prob * log($prob, 2) : 0;

    //         $entropyValues[$productId] = $entropy;
    //         $gainValues[$productId] = max(0, $totalWeighted - $entropy); // tetap heuristic
    //         $accuracy[$productId] = round(($weighted / max($transactions->max('weighted_sales'), 1)) * 100, 2);
    //     }

    //     // Bulk upsert
    //     $entropyData = [];
    //     foreach ($gainValues as $productId => $gain) {
    //         $entropyData[] = [
    //             'product_id' => $productId,
    //             'entropy' => round($entropyValues[$productId], 6),
    //             'gain' => round($gain, 6),
    //             'updated_at' => now(),
    //             'created_at' => now()
    //         ];
    //     }

    //     foreach (array_chunk($entropyData, 500) as $chunk) {
    //         EntropyGain::upsert(
    //             $chunk,
    //             ['product_id'],
    //             ['entropy', 'gain', 'updated_at']
    //         );
    //     }

    //     return response()->json([
    //         'accuracy' => $accuracy,
    //         'entropyValues' => $entropyValues,
    //         'gainValues' => $gainValues
    //     ]);
    // }

    public function countAccuracy()
    {
        // Ambil tanggal transaksi terakhir
        $tMax = Transaction::max('transaction_date');

        // Join transaction_details dan products
        $transactions = Transaction::join('transaction_details as td', 'transactions.id', '=', 'td.transaction_id')
            ->join('products as p', 'td.product_id', '=', 'p.id')
            ->selectRaw(
                'td.product_id, p.name, p.code, p.price, p.condition, p.photo, 
            SUM(td.quantity * POW(0.995, DATEDIFF(?, transactions.transaction_date))) as weighted_sales, 
            MIN(transactions.transaction_date) as first_date',
                [$tMax]
            )
            ->groupBy('td.product_id', 'p.name', 'p.code', 'p.price', 'p.condition', 'p.photo')
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json(['message' => 'Tidak ada transaksi.']);
        }

        $totalWeighted = $transactions->sum('weighted_sales');
        $entropyValues = [];
        $gainValues = [];
        $accuracy = [];
        $productsData = [];

        foreach ($transactions as $row) {
            $productId = $row->product_id;
            $sales = $row->weighted_sales;

            $ageDays = Carbon::parse($row->first_date)->diffInDays(Carbon::parse($tMax));
            $weighted = $sales * log(1 + max($ageDays, 1));

            $prob = $totalWeighted > 0 ? $weighted / $totalWeighted : 0;
            $entropy = $prob > 0 ? -$prob * log($prob, 2) : 0;

            $entropyValues[$productId] = $entropy;
            // $gainValues[$productId] = max(0, $totalWeighted - $entropy); // tetap heuristic
            $gainValues[$productId] = max(0, $entropy - log(1 + $weighted));
            // $accuracy[$productId] = round(($weighted / max($transactions->max('weighted_sales'), 1)) * 100, 2);
            $accuracy[$productId] = round(($weighted / $totalWeighted) * 100, 2);

            // Simpan data produk
            $productsData[$productId] = [
                'id' => $productId,
                'name' => $row->name,
                'code' => $row->code,
                'price' => $row->price,
                'condition' => $row->condition,
                'photo' => $row->photo
            ];
        }

        // Bulk upsert entropy & gain tetap seperti sebelumnya
        $entropyData = [];
        foreach ($gainValues as $productId => $gain) {
            $entropyData[] = [
                'product_id' => $productId,
                'entropy' => round($entropyValues[$productId], 6),
                'gain' => round($gain, 6),
                'updated_at' => now(),
                'created_at' => now()
            ];
        }

        foreach (array_chunk($entropyData, 500) as $chunk) {
            EntropyGain::upsert(
                $chunk,
                ['product_id'],
                ['entropy', 'gain', 'updated_at']
            );
        }

        return response()->json([
            'accuracy' => $accuracy,
            'entropyValues' => $entropyValues,
            'gainValues' => $gainValues,
            'products' => array_values($productsData) // dikirim sebagai array
        ]);
    }

    private function buildDecisionTree($gainValues, $accuracy, $products)
    {
        if (empty($gainValues)) {
            return "Tidak ada decision tree.";
        }

        // mengurutkan berdasarkan akurasi dari tinggi ke rendah
        $accuracy = is_array($accuracy) ? $accuracy : $accuracy->toArray(); // konversi Collection ke array
        arsort($accuracy); // urutkan dari akurasi tertinggi ke terendah

        $bestAttributeProductId = array_keys($gainValues, max($gainValues))[0];
        $rootProduct = $products[$bestAttributeProductId] ?? null;

        if ($rootProduct) {
            $rootNodeIdentifier = $rootProduct['code']; // menggunakan 'code' untuk kode produk
        } else {
            $rootNodeIdentifier = "Produk ID: " . $bestAttributeProductId; // fallback jika produk tidak ditemukan
        }
        $tree = "Root Node: $rootNodeIdentifier\n";

        foreach ($accuracy as $productId => $acc) {
            $product = $products[$productId] ?? null;
            if (!$product)
                continue;

            $productName = $product['name'];
            $priceCategory = match (true) {
                $product['price'] > 500000 => 'sangat tinggi',
                $product['price'] >= 200000 => 'tinggi',
                $product['price'] >= 50000 => 'sedang',
                $product['price'] >= 20000 => 'rendah',
                $product['price'] >= 10000 => 'sangat rendah',
                default => 'sangat rendah',
            };

            $stockCategory = match (true) {
                $product['stocks'] > 200 => 'sangat tinggi',
                $product['stocks'] >= 100 => 'tinggi',
                $product['stocks'] >= 60 => 'sedang',
                $product['stocks'] >= 15 => 'rendah',
                $product['stocks'] >= 7 => 'sangat rendah',
                default => 'sangat rendah',
            };

            $accuracyCategory = match (true) {
                $acc >= 90 => 'sangat tinggi',
                $acc >= 80 => 'tinggi',
                $acc >= 40 => 'sedang',
                $acc >= 20 => 'rendah',
                $acc >= 0 => 'sangat rendah',
                default => 'sangat rendah',
            };

            // menentukan kondisi dan rekomendasi berdasarkan kombinasi kategori
            $condition = "";
            $recommendation = "";

            if ($accuracyCategory === 'sangat tinggi') {
                if ($priceCategory === 'sangat tinggi' || $stockCategory === 'sangat tinggi') {
                    $condition = "Produk sangat laku dan bernilai tinggi. Fokus pada kestabilan distribusi dan pelayanan.";
                    $recommendation = "Pertahankan kualitas dan perkuat supply chain.";
                } else {
                    $condition = "Produk sangat laku. Pastikan stok dan harga tetap kompetitif.";
                    $recommendation = "Optimalkan pemasaran dan ketersediaan barang.";
                }
            } elseif ($accuracyCategory === 'tinggi') {
                if ($priceCategory === 'tinggi' && $stockCategory === 'tinggi') {
                    $condition = "Produk laku dan memiliki margin bagus.";
                    $recommendation = "Fokus pada iklan dan jaga kestabilan stok.";
                } else {
                    $condition = "Produk laku. Perlu perhatian pada manajemen stok atau harga.";
                    $recommendation = "Tingkatkan efisiensi dalam harga dan stok.";
                }
            } elseif ($accuracyCategory === 'sedang') {
                if ($priceCategory === 'sedang' && $stockCategory === 'sedang') {
                    $condition = "Produk sedang. Perlu strategi lebih agresif.";
                    $recommendation = "Perkuat promosi dan evaluasi harga.";
                } else {
                    $condition = "Produk lumayan laku.";
                    $recommendation = "Lakukan survei pasar untuk peningkatan.";
                }
            } elseif ($accuracyCategory === 'rendah') {
                if ($priceCategory === 'tinggi') {
                    $condition = "Produk tidak laku kemungkinan karena harga tinggi.";
                    $recommendation = "Evaluasi harga atau berikan diskon.";
                } else {
                    $condition = "Produk kurang diminati.";
                    $recommendation = "Ubah strategi pemasaran dan pertimbangkan diskon.";
                }
            } else { // sangat rendah
                if ($stockCategory === 'sangat tinggi') {
                    $condition = "Produk tidak laku tapi stok berlebihan.";
                    $recommendation = "Kurangi produksi dan lakukan cuci gudang.";
                } else {
                    $condition = "Produk tidak laku.";
                    $recommendation = "Pertimbangkan untuk menghapus produk atau ubah strategi besar-besaran.";
                }
            }

            $tree .= "|-- *$productName*
    |---Akurasi: $accuracyCategory ($acc%)
    |---Harga: $priceCategory
    |---Stok: $stockCategory
    |---Kondisi: $condition
        ├─ Rekomendasi: $recommendation\n";

            DecisionTree::updateOrCreate(
                ['product_id' => $productId],
                [
                    'accuracy_category' => $accuracyCategory,
                    'price_category' => $priceCategory,
                    'stock_category' => $stockCategory,
                    'recommendation' => $recommendation
                ]
            );

            AccuracyPrediction::updateOrCreate(
                ['product_id' => $productId],
                ['accuracy_percentage' => round($acc, 2)]
            );
        }

        return $tree;
    }
}













