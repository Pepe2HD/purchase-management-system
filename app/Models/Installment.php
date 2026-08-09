<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = ['payment_order_id', 'installment_number', 'amount', 'due_date', 'status'];

    public function paymentOrder()
    {
        return $this->belongsTo(PaymentOrder::class);
    }
}
