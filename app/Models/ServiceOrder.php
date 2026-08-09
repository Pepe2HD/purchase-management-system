<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    protected $fillable = ['description', 'status'];

    public function purchaseRequests() {
        return $this->hasMany(PurchaseRequest::class);
    }
}
