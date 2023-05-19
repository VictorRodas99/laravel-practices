<?php

use App\Http\Controllers\Api\JWTAuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InvoiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(JWTAuthController::class)->group(function () {
    Route::post("login", "login");
    Route::post("register", "register_user");
    Route::get("logout", "logout");
});

Route::group([
    "middleware" => "auth:api",
    "prefix" => "v1"
], function () {
    Route::apiResource("customers", CustomerController::class);
    Route::apiResource("invoices", InvoiceController::class);
    Route::post("invoices/bulk", [InvoiceController::class, 'bulk_store']);
});
