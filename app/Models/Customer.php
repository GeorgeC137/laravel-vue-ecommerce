<?php

namespace App\Models;

use App\Models\User;
use App\Enums\AddressType;
use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    protected $guarded = [];

    // Relationship to user
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    private function getAddresses(): HasOne
    {
        return $this->hasOne(CustomerAddress::class, 'customer_id', 'user_id');
    }

    public function shippingAddress(): HasOne
    {
        return $this->getAddresses()->where('type', '=', AddressType::Shipping);
    }

    public function billingAddress(): HasOne
    {
        return $this->getAddresses()->where('type', '=', AddressType::Billing);
    }
}
