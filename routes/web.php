<?php

use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseRequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('purchase_requests', PurchaseRequestController::class);

Route::get('purchase_requests/{purchaseRequest}/payment-orders/create', function (PurchaseRequest $purchaseRequest) {
    return view('purchase_requests.payment_orders.create', compact('purchaseRequest'));
})->name('purchase_requests.payment_orders.create');
