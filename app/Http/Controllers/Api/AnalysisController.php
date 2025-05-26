<?php

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
//
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

namespace App\Http\Controllers\API;

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

    public function countAttributes()
    {
        $transactions = Transaction::with('details.product')->get();
        $Tmax = Carbon::parse(Transaction::max('transaction_date'));

        $lambda = 0.005;
        $weightedSales = [];
        $firstTransactionDates = [];
        $lastTransactionDates = [];
        $productDateDifferences = [];
        $productTimeWeights = [];

        foreach ($transactions as $transaction) {
            $transactionDate = Carbon::parse($transaction->transaction_date);
            $selisihHari = $transactionDate->diffInDays($Tmax);
            $bobotWaktu = exp(-$lambda * $selisihHari);

            foreach ($transaction->details as $detail) {
                $productId = $detail->product->id;
                $quantity = $detail->quantity;

                // Update first and last transaction dates
                $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $transactionDate;
                $lastTransactionDates[$productId] = $lastTransactionDates[$productId] ?? $transactionDate;

                if ($transactionDate->lt($firstTransactionDates[$productId])) {
                    $firstTransactionDates[$productId] = $transactionDate;
                }
                if ($transactionDate->gt($lastTransactionDates[$productId])) {
                    $lastTransactionDates[$productId] = $transactionDate;
                }

                // Initialize weighted sales if not set
                if (!isset($weightedSales[$productId])) {
                    $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
                }
                $weightedSales[$productId]['raw'] += $quantity;
                $weightedSales[$productId]['weighted'] += $quantity * $bobotWaktu;
            }
        }

        foreach ($weightedSales as $productId => &$sales) {
            $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
            $lastTransactionDate = Carbon::parse($lastTransactionDates[$productId]);
            $selisihHari = $firstTransactionDate->diffInDays($lastTransactionDate);
            $bobotWaktuTambahan = log(1 + max($selisihHari, 1)); // Hindari log(0)

            $productDateDifferences[$productId] = $selisihHari;
            $productTimeWeights[$productId] = round($bobotWaktuTambahan, 4);
            $sales['weighted'] *= $bobotWaktuTambahan;

            SalesCount::updateOrCreate(
                ['product_id' => $productId, 'transaction_date' => $Tmax],
                [
                    'raw_sales' => $sales['raw'],
                    'weighted_sales' => $sales['weighted'],
                    'days_between_first_last_transaction' => $productDateDifferences[$productId],
                    'time_weight' => $productTimeWeights[$productId]
                ]
            );
        }

        $products = Product::whereIn('id', array_keys($weightedSales))->get()->keyBy('id');

        return response()->json(compact(
            'transactions',
            'weightedSales',
            'products',
            'firstTransactionDates',
            'lastTransactionDates',
            'productDateDifferences',
            'productTimeWeights',
            'Tmax'
        ));
    }

    public function countAccuracy()
    {
        $transactions = Transaction::with('details.product')->get();
        if ($transactions->isEmpty()) {
            return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
        }

        $tMax = Carbon::parse(Transaction::max('transaction_date'));
        $lambda = 0.007;
        $weightedSales = [];
        $firstTransactionDates = [];

        foreach ($transactions as $transaction) {
            $t = Carbon::parse($transaction->transaction_date);
            $diffDays = $t->diffInDays($tMax);
            $weight = exp(-$lambda * $diffDays);

            foreach ($transaction->details as $detail) {
                $productId = $detail->product->id;
                $quantity = $detail->quantity;
                $firstTransactionDates[$productId] = $firstTransactionDates[$productId] ?? $t;

                if ($t->lt($firstTransactionDates[$productId])) {
                    $firstTransactionDates[$productId] = $t;
                }

                $weightedSales[$productId] = ($weightedSales[$productId] ?? 0) + ($quantity * $weight);
            }
        }

        foreach ($weightedSales as $productId => &$sales) {
            $firstTransactionDate = Carbon::parse($firstTransactionDates[$productId]);
            $productAge = $firstTransactionDate->diffInDays($tMax);
            $sales *= log(1 + max($productAge, 1)); // Hindari log(0)
        }

        if (empty($weightedSales)) {
            return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
        }

        $maxWeightedSales = max($weightedSales) ?: 1; // Hindari divide by zero
        $accuracy = collect($weightedSales)->map(fn($s) => round(($s / $maxWeightedSales) * 100, 2));

        $totalSales = array_sum($weightedSales);
        $entropyValues = [];
        $epsilon = 1e-10; // Hindari log(0)

        foreach ($weightedSales as $productId => $sales) {
            $probability = $sales / $totalSales;
            $entropyValues[$productId] = -$probability * log($probability + $epsilon, 2);
        }

        $overallEntropy = array_sum($entropyValues);
        $gainValues = [];

        foreach ($entropyValues as $productId => $entropy) {
            $gainValues[$productId] = $overallEntropy - $entropy;
        }

        foreach ($gainValues as $productId => $gain) {
            EntropyGain::updateOrCreate(
                ['product_id' => $productId],
                [
                    'entropy' => round($entropyValues[$productId], 6),
                    'gain' => round($gain, 6)
                ]
            );
        }

        // Stok Per Expired
        $productsData = Product::whereIn('id', array_keys($weightedSales))
            ->with('stocks')
            ->get();

        $products = $productsData->keyBy('id')->map(function ($product) {
            return [
                'name' => $product->name,
                'code' => $product->code,
                'condition' => $product->condition,
                'price' => $product->price,
                'photo' => $product->photo,
                'category_name' => $product->category->name,
                'stocks' => $product->stocks->sum('stock')
            ];
        });

        // Cek accuracy > 90% → buat notifikasi dan simpan
        $highAccuracyNotifications = [];

        foreach ($accuracy as $productId => $accValue) {
            if ($accValue > 85) {
                $product = Product::find($productId);
                if ($product) {
                    $message = "{$product->name} berpeluang {$accValue}% menjadi produk terlaris.";

                    // Notification::create([
                    //     'message' => $message,
                    //     'notification_type' => 'Produk Terlaris'
                    // ]);

                    // $highAccuracyNotifications[] = $message;

                    $existing = Notification::where('message', $message)
                        ->where('notification_type', 'Produk Terlaris')
                        ->first();

                    if (!$existing) {
                        Notification::create([
                            'message' => $message,
                            'notification_type' => 'Produk Terlaris',
                            'notification_time' => now()
                        ]);
                        $highAccuracyNotifications[] = $message;
                    }
                }
            }
        }

        $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

        // return response()->json(compact('accuracy', 'products', 'entropyValues', 'gainValues', 'decisionTree'));
        return response()->json([
            'accuracy' => $accuracy,
            'products' => $products,
            'entropyValues' => $entropyValues,
            'gainValues' => $gainValues,
            'decisionTree' => $decisionTree,
            'notifications' => $highAccuracyNotifications, // Tambahkan ke response
        ]);
    }

    private function buildDecisionTree($gainValues, $accuracy, $products)
    {
        if (empty($gainValues)) {
            return "Tidak ada decision tree.";
        }

        $bestAttribute = array_keys($gainValues, max($gainValues))[0];
        $tree = "Root Node: $bestAttribute\n";

        foreach ($accuracy as $productId => $acc) {
            $product = $products[$productId] ?? null;
            if (!$product)
                continue;

            $productName = $product['name'];
            $priceCategory = match (true) {
                $product['price'] > 200000 => 'tinggi',
                $product['price'] >= 100000 => 'sedang',
                default => 'rendah',
            };

            $stockCategory = match (true) {
                $product['stocks'] > 100 => 'tinggi',
                $product['stocks'] >= 20 => 'sedang',
                default => 'rendah',
            };

            $accuracyCategory = match (true) {
                $acc >= 85 => 'tinggi',
                $acc >= 60 => 'sedang',
                default => 'rendah',
            };

            $tree .= "|-- *$productName*
    |---Akurasi: $accuracyCategory ($acc%)
    |---Harga: $priceCategory
    |---Stok: $stockCategory\n";

            if ($accuracyCategory == "tinggi") {
                $tree .= "              ├─ Kondisi: Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.\n";
            } elseif ($accuracyCategory == "sedang") {
                $tree .= "              ├─ Kondisi: Perlu strategi pemasaran lebih agresif.\n";
            } else {
                $tree .= "              ├─ Kondisi: Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.\n";
            }

            $recommendation = match ($accuracyCategory) {
                'tinggi' => "Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.",
                'sedang' => "Perlu strategi pemasaran lebih agresif.",
                default => "Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.",
            };

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

    //=====================================================================================
    //=====================================================================================
    //=====================================================================================

    // public function getTransactions()
    // {
    //     $transactions = $this->getAllTransactionsWithDetails();
    //     return response()->json(['transactions' => $transactions]);
    // }

    // private function getAllTransactionsWithDetails()
    // {
    //     return Transaction::with('details.product')->get();
    // }

    // public function countAttributes()
    // {
    //     $transactions = $this->getAllTransactionsWithDetails();
    //     $tMax = Carbon::parse(Transaction::max('transaction_date'));

    //     $lambda = 0.005;
    //     [$weightedSales, $firstDates, $lastDates, $diffDaysMap, $weightsMap] = $this->calculateWeightedSales($transactions, $lambda, $tMax);

    //     // Simpan ke database
    //     $this->storeSalesCount($weightedSales, $firstDates, $lastDates, $tMax);

    //     $products = $this->getProductMetadata(array_keys($weightedSales));

    //     return response()->json([
    //         'Tmax' => $tMax,
    //         'weightedSales' => $weightedSales,
    //         'products' => $products,
    //         'firstTransactionDates' => $firstDates,
    //         'lastTransactionDates' => $lastDates,
    //         'productDateDifferences' => $diffDaysMap,
    //         'productTimeWeights' => $weightsMap,
    //     ]);
    // }

    // public function countAccuracy()
    // {
    //     $transactions = $this->getAllTransactionsWithDetails();
    //     if ($transactions->isEmpty()) {
    //         return response()->json(['message' => 'Tidak ada prediksi karena tidak ada data transaksi.']);
    //     }

    //     $tMax = Carbon::parse(Transaction::max('transaction_date'));
    //     $lambda = 0.007;

    //     [$weightedSales] = $this->calculateWeightedSales($transactions, $lambda, $tMax);

    //     // Normalisasi dan hitung entropy
    //     [$accuracy, $entropyValues, $gainValues] = $this->calculateEntropyGain($weightedSales, $tMax);

    //     // Simpan notifikasi
    //     $highAccuracyNotifications = [];
    //     foreach ($accuracy as $productId => $accValue) {
    //         if ($accValue > 85) {
    //             $product = Product::find($productId);
    //             if ($product) {
    //                 $message = "{$product->name} berpeluang {$accValue}% menjadi produk terlaris.";
    //                 $existing = Notification::where('message', $message)->first();

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

    //     $products = $this->getProductMetadata(array_keys($weightedSales));

    //     $decisionTree = $this->buildDecisionTree($gainValues, $accuracy, $products);

    //     return response()->json(compact(
    //         'accuracy',
    //         'products',
    //         'entropyValues',
    //         'gainValues',
    //         'decisionTree',
    //         'highAccuracyNotifications'
    //     ));
    // }
    // private function calculateWeightedSales($transactions, $lambda, $tMax)
    // {
    //     $weightedSales = [];
    //     $firstDates = [];
    //     $lastDates = [];
    //     $diffDaysMap = [];
    //     $weightsMap = [];

    //     foreach ($transactions as $transaction) {
    //         $t = Carbon::parse($transaction->transaction_date);
    //         $diff = $t->diffInDays($tMax);
    //         $weight = exp(-$lambda * $diff);

    //         foreach ($transaction->details as $detail) {
    //             $productId = $detail->product->id;
    //             $quantity = $detail->quantity;

    //             $firstDates[$productId] = $firstDates[$productId] ?? $t;
    //             $lastDates[$productId] = $lastDates[$productId] ?? $t;

    //             if ($t->lt($firstDates[$productId]))
    //                 $firstDates[$productId] = $t;
    //             if ($t->gt($lastDates[$productId]))
    //                 $lastDates[$productId] = $t;

    //             if (!isset($weightedSales[$productId])) {
    //                 $weightedSales[$productId] = ['raw' => 0, 'weighted' => 0];
    //             }

    //             $weightedSales[$productId]['raw'] += $quantity;
    //             $weightedSales[$productId]['weighted'] += $quantity * $weight;
    //         }
    //     }

    //     foreach ($weightedSales as $productId => &$sales) {
    //         $diffDays = $firstDates[$productId]->diffInDays($lastDates[$productId]);
    //         $timeWeight = log(1 + max($diffDays, 1));
    //         $sales['weighted'] *= $timeWeight;

    //         $diffDaysMap[$productId] = $diffDays;
    //         $weightsMap[$productId] = round($timeWeight, 4);
    //     }

    //     return [$weightedSales, $firstDates, $lastDates, $diffDaysMap, $weightsMap];
    // }
    // private function storeSalesCount(array $weightedSales, array $firstDates, array $lastDates, $tMax)
    // {
    //     foreach ($weightedSales as $productId => $sales) {
    //         $product = Product::find($productId);
    //         if (!$product)
    //             continue;

    //         SalesCount::updateOrCreate(
    //             ['product_id' => $productId],
    //             [
    //                 'product_name' => $product->name,
    //                 'raw_sales' => $sales['raw'],
    //                 'weighted_sales' => $sales['weighted'],
    //                 'first_transaction_date' => $firstDates[$productId],
    //                 'last_transaction_date' => $lastDates[$productId],
    //                 'tmax' => $tMax
    //             ]
    //         );
    //     }
    // }
    // private function calculateEntropyGain(array $weightedSales, $tMax)
    // {
    //     $weightedOnly = array_column($weightedSales, 'weighted');
    //     $total = array_sum($weightedOnly);
    //     if ($total == 0)
    //         $total = 1;

    //     $normalized = [];
    //     foreach ($weightedSales as $productId => $data) {
    //         $normalized[$productId] = $data['weighted'] / $total;
    //     }

    //     $entropyValues = [];
    //     $gainValues = [];
    //     $accuracy = [];

    //     $baseEntropy = 0;
    //     foreach ($normalized as $prob) {
    //         if ($prob > 0)
    //             $baseEntropy -= $prob * log($prob, 2);
    //     }

    //     foreach ($normalized as $productId => $prob) {
    //         $entropy = $prob > 0 ? -$prob * log($prob, 2) : 0;
    //         $gain = $baseEntropy - $entropy;

    //         $entropyValues[$productId] = round($entropy, 4);
    //         $gainValues[$productId] = round($gain, 4);

    //         $accuracy[$productId] = round($prob * 100, 2);
    //     }

    //     return [$accuracy, $entropyValues, $gainValues];
    // }
    // private function getProductMetadata(array $productIds)
    // {
    //     return Product::whereIn('id', $productIds)->get(['id', 'name', 'price']);
    // }

    // private function buildDecisionTree($gainValues, $accuracy, $products)
    // {
    //     if (empty($gainValues)) {
    //         return "Tidak ada decision tree.";
    //     }

    //     $bestAttribute = array_keys($gainValues, max($gainValues))[0];
    //     $tree = "Root Node: $bestAttribute\n";

    //     foreach ($accuracy as $productId => $acc) {
    //         $product = $products[$productId] ?? null;
    //         if (!$product)
    //             continue;

    //         $productName = $product['name'];
    //         $priceCategory = match (true) {
    //             $product['price'] > 200000 => 'tinggi',
    //             $product['price'] >= 100000 => 'sedang',
    //             default => 'rendah',
    //         };

    //         $stockCategory = match (true) {
    //             $product['stocks'] > 100 => 'tinggi',
    //             $product['stocks'] >= 20 => 'sedang',
    //             default => 'rendah',
    //         };

    //         $accuracyCategory = match (true) {
    //             $acc >= 85 => 'tinggi',
    //             $acc >= 60 => 'sedang',
    //             default => 'rendah',
    //         };

    //         $tree .= "|-- *$productName*
    // |---Akurasi: $accuracyCategory ($acc%)
    // |---Harga: $priceCategory
    // |---Stok: $stockCategory\n";

    //         if ($accuracyCategory == "tinggi") {
    //             $tree .= "              ├─ Kondisi: Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.\n";
    //         } elseif ($accuracyCategory == "sedang") {
    //             $tree .= "              ├─ Kondisi: Perlu strategi pemasaran lebih agresif.\n";
    //         } else {
    //             $tree .= "              ├─ Kondisi: Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.\n";
    //         }

    //         $recommendation = match ($accuracyCategory) {
    //             'tinggi' => "Produk ini sangat menguntungkan! Optimalkan pemasaran & atur stok.",
    //             'sedang' => "Perlu strategi pemasaran lebih agresif.",
    //             default => "Tidak laku dan stok berlebih. Evaluasi apakah perlu dihentikan atau diskon besar.",
    //         };

    //         DecisionTree::updateOrCreate(
    //             ['product_id' => $productId],
    //             [
    //                 'accuracy_category' => $accuracyCategory,
    //                 'price_category' => $priceCategory,
    //                 'stock_category' => $stockCategory,
    //                 'recommendation' => $recommendation
    //             ]
    //         );

    //         AccuracyPrediction::updateOrCreate(
    //             ['product_id' => $productId],
    //             ['accuracy_percentage' => round($acc, 2)]
    //         );
    //     }
    //     return $tree;
    // }
}


