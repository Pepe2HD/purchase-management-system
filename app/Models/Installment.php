<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    public function paymentOrder() {
        return $this->belongsTo(PaymentOrder::class);
    }
}
