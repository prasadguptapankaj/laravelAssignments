<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ShippingAddress;
use App\Models\BillingAddress;
use App\Models\Payment;
use App\Models\OrderItem;
use App\Models\Customer;

class Order extends Model
{
    use HasFactory;


    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function billingAddress()
    {
        return $this->belongsTo(BillingAddress::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
