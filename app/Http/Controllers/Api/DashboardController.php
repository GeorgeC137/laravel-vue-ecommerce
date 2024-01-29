<?php

namespace App\Http\Controllers\Api;

use App\Enums\CustomerStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function activeCustomers()
    {
        return Customer::where('status', CustomerStatus::Active)->count();
    }

    public function activeProducts()
    {
        // TODO Implement where for Active Products
        return Product::count();
    }

    public function paidOrders()
    {
        return Order::where('status', OrderStatus::Paid)->count();
    }

    public function totalIncome()
    {
        return Order::where('status', OrderStatus::Paid)->sum('total_price');
    }
}
