<?php

namespace App\Enums;

enum OrderStatus: string
{
    const Paid = 'paid';
    const Completed = 'complete';
    const Unpaid = 'unpaid';
}
