<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes (trang Blade và các endpoint AJAX trả JSON)
|--------------------------------------------------------------------------
| Tên hàm Controller lấy từ Class Diagram của nhóm.
| Hàm ghi chú "(thêm mới)" là hàm hiển thị trang, chưa có trong Class Diagram.
|
| Quy ước tham số route, khai báo một lần trong AppServiceProvider::boot():
|   Route::pattern('id', '[0-9]+');       // {id} luôn là số
|   Route::pattern('key', '[a-z0-9_]+');  // {key} là khóa chuỗi của bảng size (chart_key, ví dụ shoe_vn)
*/

/* ===================== 1. CÔNG KHAI (Guest + Customer) ===================== */

Route::get('/', [ProductController::class, 'home'])->name('home');                 // (thêm mới) bán chạy + mới nhất
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
// Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,1')->name('contact.send');

// Trang giày và trang phụ kiện dùng chung ProductController@list, khác nhau ở tham số 'group'
// (menu lọc loại phụ kiện do controller nạp thẳng danh mục vào view, không cần route riêng)
Route::get('/products', [ProductController::class, 'list'])
    ->defaults('group', 'shoe')->name('products.index');
Route::get('/accessories', [ProductController::class, 'list'])
    ->defaults('group', 'accessory')->name('accessories.index');
Route::get('/products/{id}', [ProductController::class, 'getById'])->name('products.show');

// Đánh giá của một sản phẩm (AJAX: lọc theo sao, "hiển thị thêm")
Route::get('/products/{id}/reviews', [ReviewController::class, 'listByProduct'])->name('products.reviews');

// Bảng chọn size (pop-up): nạp bằng AJAX (trả HTML) khi bấm "Hướng dẫn chọn kích cỡ" hoặc từ gợi ý của chatbot
Route::get('/categories/{key}/size-chart', [CategoryController::class, 'getSizeChart'])->name('categories.size-chart');

// Chatbot AI: Guest dùng tự do, giới hạn tần suất (Guest không có lịch sử, xem route chatbot.history ở mục 4)
Route::post('/chatbot/recommend', [AiController::class, 'suggest'])
    ->middleware('throttle:10,1')->name('chatbot.recommend');

/* ===================== 2. GIỎ HÀNG (Guest + Customer) ===================== */
// Guest: giỏ lưu theo token trong Cookie (carts.session_token). Customer: theo user_id.

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'getMine'])->name('index');
    Route::post('/add', [CartController::class, 'addItem'])->name('add');          // Seq03
    Route::patch('/items/{id}', [CartController::class, 'updateItem'])->name('update');
    Route::delete('/items/{id}', [CartController::class, 'removeItem'])->name('remove');
});

/* ===================== 3. TÀI KHOẢN ===================== */

// Trang Tài khoản: Guest thấy form đăng nhập/đăng ký, đã đăng nhập thấy thông tin cá nhân
// kèm danh sách "Đơn hàng của tôi" (UserController@getById nạp cả đơn hàng của người dùng).
// Route này PHẢI công khai (không để trong nhóm 'auth') vì Guest bị chuyển về đây
// (xem redirectGuestsTo('/account') trong bootstrap/app.php).
Route::get('/account', [UserController::class, 'getById'])->name('account');

// Chỉ dành cho người CHƯA đăng nhập
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/* ===================== 4. CẦN ĐĂNG NHẬP (Customer, Admin) ===================== */

Route::middleware(['auth', 'not.locked'])->group(function () {
    // Hồ sơ
    Route::put('/account', [UserController::class, 'update'])->name('account.update');
    Route::put('/account/password', [UserController::class, 'changePassword'])->name('account.password');

    // Thanh toán (Guest bấm "Thanh toán" sẽ bị chuyển về /account, đăng nhập xong quay lại đây)
    // Form thanh toán gửi bằng AJAX. COD: JSON chứa đường dẫn, JS chuyển về /account kèm thông báo.
    // Chuyển khoản: JSON chứa URL mã QR, JS hiện QR ngay tại trang thanh toán. Không có trang "đặt hàng thành công" riêng.
    Route::get('/checkout', [OrderController::class, 'showCheckout'])->name('checkout');       // (thêm mới)
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.store');     // Seq04

    // Hủy đơn: nút "Hủy đơn" nằm trong bảng "Đơn hàng của tôi" ở trang /account
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel'); // Seq05

    // Đánh giá (chưa đăng nhập + gửi AJAX có header Accept: application/json -> Laravel trả 401 JSON)
    Route::post('/reviews', [ReviewController::class, 'create'])->name('reviews.store');         // Seq07

    // Lịch sử chatbot: chỉ người đã đăng nhập (Guest dùng chatbot nhưng không có lịch sử)
    Route::get('/chatbot/history', [AiController::class, 'listMine'])->name('chatbot.history');
});

/* ===================== 5. ADMIN ===================== */

// Trang đăng nhập admin: phải nằm NGOÀI nhóm auth/role:admin (nếu không sẽ bị chuyển hướng vòng)
// Chỉ cho tài khoản có role = admin vào (kiểm tra trong AuthController@adminLogin)
Route::middleware('guest')->prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');       // (thêm mới)
    Route::post('/login', [AuthController::class, 'adminLogin'])
        ->middleware('throttle:10,1')->name('admin.login.submit');                              // (thêm mới)
});

Route::middleware(['auth', 'not.locked', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(base_path('routes/admin.php'));