<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;

class Customer extends Model
{
    use HasFactory;

    public function billingAddresses()
    {
        return $this->hasMany(BillingAddress::class);
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }
}
