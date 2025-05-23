<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImpressionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\PasswordResetController;


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
          
// Google OAuth
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::post('/auth/google', [SocialAuthController::class, 'handleGoogleToken']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
Route::post('/auth/google', [SocialAuthController::class, 'handleGoogleToken']);

// routes/web.php
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);


/////// Notifications 
Route::prefix('admin')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});