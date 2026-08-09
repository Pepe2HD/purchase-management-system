<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    public function payments() {
        return $this->hasMany(PaymentOrder::class);
    }

    public function serviceOrder() {
        return $this->belongsTo(ServiceOrder::class);
    }
}
