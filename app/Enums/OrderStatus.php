<?php

namespace App\Enums;

enum OrderStatus: string
{
    const Paid = 'paid';
    const Completed = 'completed';
    const Unpaid = 'unpaid';
    const Cancelled = 'cancelled';
    const Shipped = 'shipped';

    public static function getStatuses()
    {
        return [
            self::Paid,
            self::Unpaid,
            self::Cancelled,
            self::Shipped,
            self::Completed,
        ];
    }
}
