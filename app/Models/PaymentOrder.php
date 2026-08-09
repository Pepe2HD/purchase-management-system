<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    public function purchaseRequest() {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function installments() {
        return $this->hasMany(Installment::class);
    }
}
