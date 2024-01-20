<?php

namespace App\Models;

use App\Models\User;
use App\Enums\AddressType;
use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'status',
    ];

    // Relationship to user
    public function user()
    {
        return $this->belongsTo(User::class);
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
