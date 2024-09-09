<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaSTK extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'mpesa_s_t_k_s';
}
