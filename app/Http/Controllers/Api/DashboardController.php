<?php

namespace App\Http\Controllers\Api;

use App\Enums\AddressType;
use App\Enums\CustomerStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function ordersByCountry()
    {
        $orders = Order::query()
            ->select(['c.name', DB::raw('count(orders.id) as count')])
            ->join('users', 'created_by', '=', 'users.id')
            ->join('customer_addresses AS a', 'users.id', '=', 'a.customer_id')
            ->join('countries AS c', 'a.country_code', '=', 'c.code')
            ->groupBy('c.name')
            ->where('status', OrderStatus::Paid)
            ->where('a.type', AddressType::Billing)
            ->get();

        return $orders;
    }

    public function latestCustomers()
    {
        return Customer::query()
            ->select(['id', 'first_name', 'last_name', 'u.email', 'phone', 'customers.created_at'])
            ->join('users AS u', 'u.id', '=', 'customers.user_id')
            ->where('status', CustomerStatus::Active)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }
}
