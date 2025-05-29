<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImpressionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\Auth\SocialAuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::post("/products",[ProductController::class,"store"]);
// Route::get("/AllProducts",[ProductController::class,"postsListe"]);
// Products
Route::apiResource('products', App\Http\Controllers\ProductController::class);
Route::post('/orders/from-cart', [OrderController::class, 'storeCartOrder']);
Route::post('/admin/products/{product}/add-images', [ProductController::class, 'addImages']);
Route::get('/products/{id}/images', [ProductController::class, 'getImages']);
Route::get("/product/{id}/impressions",[ProductController::class,"GetImpressions"]);
Route::delete('/products/{product}/images/{image}', [ProductController::class, 'deleteImage']);

//Auth Part
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// reviews 
Route::post('/submitImpression', [ImpressionController::class, 'saveImpression']);
// Orders
Route::apiResource('orders', App\Http\Controllers\OrderController::class);

// Payments
Route::apiResource('payments', App\Http\Controllers\PaymentController::class);

// Users (optional for admin control)
Route::apiResource('users', App\Http\Controllers\UserController::class);

//admin
Route::middleware(['auth', 'AdminMiddleware'])->get('/api/orders', [OrderController::class, 'index']);
Route::post('/admin/login', [AuthController::class, 'login']);
/// multi-items order
Route::post('/orders/panier', [OrderController::class, 'PanierOrder']);



/////////////auth


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
          


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});


////////// Notifications part

    // Notifications routes
    Route::middleware('auth:sanctum')->get('/admin/notifications', [NotificationController::class, 'index']);
    Route::middleware('auth:sanctum')->post('/admin/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::middleware('auth:sanctum')->patch('/admin/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);


///////// google auth inchaallah

// Redirect to Google
Route::get('/auth/google', function () {
    return Socialite::driver('google')->stateless()->redirect();
});
// Callback from Google
Route::get('/auth/google/callback', function (Request $request) {
    //dd($request);
    $googleUser = Socialite::driver('google')->stateless()->user();

    // Check user in DB or create
    $user = \App\Models\User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        ['name' => $googleUser->getName()]
    );

    // Generate token or session (JWT or sanctum)
    
    $token = $user->createToken('google-token')->plainTextToken;

    return redirect("http://localhost:3000/google/callback?token=$token");
});


////////// logged in user orders history
Route::get('/ordersHistory/userPhone', [OrderController::class, 'getUserOrders'])->middleware('auth:sanctum');
Route::get('/ordersHistory/userPhone', [OrderController::class, 'getUserOrders'])->middleware('auth:sanctum');
Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->middleware('auth:sanctum');