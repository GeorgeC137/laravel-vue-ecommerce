<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'user_id', 'customer_id');
    }
}
