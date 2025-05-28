<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\API\AnalysisController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ExitProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\EntryProductController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/active', [ProductController::class, 'indexActiveProduct']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/category/{id}', [ProductController::class, 'showByCategory']);
    Route::get('/active/category/{id}', [ProductController::class, 'showByCategoryActiveProduct']);
    Route::post('/{id}', [ProductController::class, 'update']);
    // Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::put('/updateCondition/{id}', [ProductController::class, 'updateCondition']);
    Route::get('/product/{id}/exp-dates', [ProductController::class, 'getExpDates']);
    // Route::get('/product/{product_id}/total-stock', [ProductController::class, 'getTotalStock']);
    // Route::get('/product/{product_id}/stock/{exp_date}', [ProductController::class, 'getStockByDate']);
});

Route::get('/product-stocks-report', [ProductController::class, 'productStocksReport']);
Route::get('/nonactive-history', [ProductController::class, 'getNonactiveProducts']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('toggle-favorite/{productId}', [ProductController::class, 'toggleFavorite']);
    Route::get('/check-favorite/{productId}', [ProductController::class, 'checkFavorite']);
    Route::get('/user-favorites', [ProductController::class, 'getAllFavorites']);
    // Route::get('/favorites', [ProductController::class, 'getFavorites']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/store', [EmployeeController::class, 'store']);
    Route::get('/{id}', [EmployeeController::class, 'show']);
    Route::put('/{id}', [EmployeeController::class, 'update']);
    Route::delete('/{id}', [EmployeeController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::get('transactions/{id}', [TransactionController::class, 'show']);
    Route::post('transactions/{id}/status', [TransactionController::class, 'addStatus']);
    Route::get('transactions/{id}/status-history', [TransactionController::class, 'showStatusHistory']);
    Route::put('transactions/status-history/{id}', [TransactionController::class, 'editStatus']);
    Route::delete('transactions/status-history/{id}', [TransactionController::class, 'deleteStatus']);
    Route::post('transactions/{id}/final-save', [TransactionController::class, 'finalSave']);
    Route::get('/transactions/{id}/check-final', [TransactionController::class, 'checkFinalStatus']);
    Route::get('/dashboard', [TransactionController::class, 'dashboard']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::put('/cart/update/{id}', [CartController::class, 'updateCart']);
    // Route::delete('/cart/delete/{id}', [CartController::class, 'deleteCart']);
    Route::delete('/cart/delete/product/{productId}', [CartController::class, 'deleteCart']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);
    Route::get('/cart/show', [CartController::class, 'show']);
    Route::patch('/cart/update-field', [CartController::class, 'updateField']);
});

Route::prefix('analysis')->group(function () {
    Route::get('/getTransactions', [AnalysisController::class, 'getTransactions']);
    Route::get('/countAttributes', [AnalysisController::class, 'countAttributes']);
    Route::get('/results', [AnalysisController::class, 'countAccuracy']);
});

Route::prefix('entry-products')->group(function () {
    Route::get('/', [EntryProductController::class, 'index']);
    Route::post('/store', [EntryProductController::class, 'store']);
    Route::put('/{id}', [EntryProductController::class, 'update']);
    Route::delete('/{id}', [EntryProductController::class, 'destroy']);
});

Route::prefix('exit-products')->group(function () {
    Route::get('/', [ExitProductController::class, 'index']);
    Route::post('/store', [ExitProductController::class, 'store']);
    Route::put('/{id}', [ExitProductController::class, 'update']);
    Route::delete('/{id}', [ExitProductController::class, 'destroy']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/signup', [AuthController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/signin', [AuthController::class, 'signin']);
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::delete('/signout', action: [AuthController::class, 'destroy'])->middleware('auth:sanctum');
    Route::get('/users', [AuthController::class, 'showAllUser']);
    Route::get('/users/{id}', [AuthController::class, 'showUserById']);
    Route::middleware('auth:sanctum')->put('/user/update', [AuthController::class, 'updateProfile']);
    Route::middleware('auth:sanctum')->put('/user/password', [AuthController::class, 'updatePassword']);
    Route::middleware('auth:sanctum')->post('/user/update-profile-image', [AuthController::class, 'updateProfileImage']);
    Route::middleware('auth:sanctum')->get('/user/profile-image', [AuthController::class, 'getProfileImage']);
    Route::post('/forgot-password', [AuthController::class, 'getVerificationCode']);
    Route::post('/verify-code', [AuthController::class, 'validateVerificationCode']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::put('/{id}', [NotificationController::class, 'read']);
});