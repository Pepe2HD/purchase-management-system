<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $fillable = ['description', 'service_order_id', 'status'];

    public function payments() {
        return $this->hasMany(PaymentOrder::class);
    }

    public function serviceOrder() {
        return $this->belongsTo(ServiceOrder::class);
    }
}