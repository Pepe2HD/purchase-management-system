<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    protected $fillable = ['purchase_request_id', 'type', 'total_amount'];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
