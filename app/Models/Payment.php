<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'order_id',
        'status',
        'session_id',
        'amount',
        'created_by',
        'updated_by'
    ];

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
