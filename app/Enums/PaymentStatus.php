<?php

namespace App\Enums;

enum PaymentStatus: string
{
    const Pending = 'pending';
    const Paid = 'paid';
    const Failed = 'failed';
}
