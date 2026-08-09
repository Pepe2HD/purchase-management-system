<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseRequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('purchase_requests', PurchaseRequestController::class);
