<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Enums\AddressType;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use App\Enums\CustomerStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\OrderResource;
use App\Traits\ReportTrait;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ReportTrait;

    public function activeCustomers()
    {
        return Customer::where('status', CustomerStatus::Active)->count();
    }

    public function activeProducts()
    {
        // TODO Implement where for Active Products
        return Product::where('published', '=', 1)->count();
    }

    public function paidOrders()
    {
        $fromDate = $this->getFromDate();

        $query =  Order::query()->where('status', OrderStatus::Paid);

        if ($fromDate) {
            $query->where('created_at', '>', $fromDate);
        }

        return $query->count();
    }

    public function totalIncome()
    {
        $fromDate = $this->getFromDate();

        $query = Order::where('status', OrderStatus::Paid);

        if ($fromDate) {
            $query->where('created_at', '>', $fromDate);
        }

        return $query->sum('total_price');
    }

    public function ordersByCountry()
    {
        $fromDate = $this->getFromDate();

        $query = Order::query()
            ->select(['c.name', DB::raw('count(orders.id) as count')])
            ->join('users AS u', 'created_by', '=', 'u.id')
            ->join('customer_addresses AS a', 'u.id', '=', 'a.customer_id')
            ->join('countries AS c', 'a.country_code', '=', 'c.code')
            ->groupBy('c.name')
            ->where('status', OrderStatus::Paid)
            ->where('a.type', AddressType::Billing);

        if ($fromDate) {
            $query->where('orders.created_at', '>', $fromDate);
        }

        return $query->get();
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

    public function latestOrders()
    {
        return OrderResource::collection(
            Order::query()
                ->select(['o.id', 'o.total_price', 'o.created_at', 'c.first_name', 'c.last_name', DB::raw('count(oi.id) as items'), 'c.user_id'])
                ->join('order_items AS oi', 'oi.order_id', '=', 'o.id')
                ->join('customers AS c', 'c.user_id', '=', 'o.created_by')
                ->from('orders AS o')
                ->where('o.status', OrderStatus::Paid)
                ->orderBy('o.created_at', 'desc')
                ->limit(10)
                ->groupBy(['o.id', 'o.total_price', 'o.created_at', 'c.first_name', 'c.last_name', 'c.user_id'])
                ->get()
        );
    }
}
