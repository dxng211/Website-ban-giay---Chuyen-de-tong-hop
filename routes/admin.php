<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Báo cáo thống kê
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Quản lý sản phẩm và tồn kho (UC09): mỗi form là một trang riêng
Route::get('/products', [ProductController::class, 'list'])
    ->defaults('scope', 'admin')->name('products.index');
Route::get('/products/create', [ProductController::class, 'showCreateForm'])->name('products.create');   // (thêm mới)
Route::post('/products', [ProductController::class, 'create'])->name('products.store');                  // Seq06
Route::get('/products/{id}/edit', [ProductController::class, 'showEditForm'])->name('products.edit');    // (thêm mới)
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');             // Seq06
Route::get('/products/{id}/stock', [ProductController::class, 'showStock'])->name('products.stock.edit'); // (thêm mới)
Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock'])->name('products.stock');
Route::delete('/products/{id}', [ProductController::class, 'close'])->name('products.close');            // xóa mềm

// Quản lý đơn hàng (UC10)
Route::get('/orders', [OrderController::class, 'list'])
    ->defaults('scope', 'admin')->name('orders.index');
Route::get('/orders/{id}', [OrderController::class, 'getById'])->name('orders.show');                    // trang chi tiết đơn
Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');           // Seq05 (Admin)
Route::patch('/orders/{id}/payment', [PaymentController::class, 'confirmPayment'])->name('orders.payment');

// Quản lý đánh giá (UC10)
Route::get('/reviews', [ReviewController::class, 'list'])->name('reviews.index');
Route::patch('/reviews/{id}/status', [ReviewController::class, 'updateStatus'])->name('reviews.status');

// Quản lý danh mục (thêm mới)
Route::get('/categories', [CategoryController::class, 'list'])
    ->defaults('scope', 'admin')->name('categories.index');
Route::get('/categories/create', [CategoryController::class, 'showCreateSizeChartForm'])->name('categories.create');
Route::post('/categories', [CategoryController::class, 'createSizeChart'])->name('categories.store');
Route::get('/categories/{id}/edit', [CategoryController::class, 'list'])->name('categories.edit');
Route::put('/categories/{id}', [CategoryController::class, 'updateSizeChart'])->name('categories.update');
Route::delete('/categories/{id}', [CategoryController::class, 'updateSizeChart'])->name('categories.destroy');

// Quản lý nhóm bảng size: size_chart_groups + các dòng size_charts (thêm mới)
// Tham số là {key} (chart_key dạng chuỗi như shoe_vn), KHÔNG dùng {id} số: xem Route::pattern('key', ...) trong AppServiceProvider
// Dùng chung CategoryController vì bảng size thuộc về danh mục
Route::get('/size-charts', [CategoryController::class, 'listSizeCharts'])->name('size-charts.index');
Route::get('/size-charts/create', [CategoryController::class, 'showCreateSizeChartForm'])->name('size-charts.create');
Route::post('/size-charts', [CategoryController::class, 'createSizeChart'])->name('size-charts.store');
Route::get('/size-charts/{key}/edit', [CategoryController::class, 'showEditSizeChartForm'])->name('size-charts.edit');
Route::put('/size-charts/{key}', [CategoryController::class, 'updateSizeChart'])->name('size-charts.update');     // lưu nhóm + thay toàn bộ các dòng
Route::delete('/size-charts/{key}', [CategoryController::class, 'deleteSizeChart'])->name('size-charts.destroy'); // chặn nếu còn danh mục đang dùng

// Quản lý người dùng
Route::get('/users', [UserController::class, 'list'])->name('users.index');                              // (thêm mới)
Route::patch('/users/{id}/lock', [UserController::class, 'lockAccount'])->name('users.lock');
Route::patch('/users/{id}/unlock', [UserController::class, 'unlockAccount'])->name('users.unlock');