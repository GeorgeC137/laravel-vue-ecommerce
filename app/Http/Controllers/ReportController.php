<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Traits\ReportTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ReportTrait;

    public function orders()
    {
        $query = Order::query()
            ->select([DB::raw('CAST(created_at AS DATE) as day'), DB::raw('count(id) as count')])
            ->groupBy(DB::raw('CAST(created_at AS DATE)'));

        return $this->prepareDataForCharts($query, 'Orders By Day');
    }

    public function customers()
    {
        $query = Customer::query()
            ->select([DB::raw('CAST(created_at AS DATE) as day'), DB::raw('count(user_id) as count')])
            ->groupBy(DB::raw('CAST(created_at AS DATE)'));

        return $this->prepareDataForCharts($query, 'Customers By Day');
    }

    private function prepareDataForCharts($query, $label)
    {
        $fromDate = $this->getFromDate() ?: Carbon::now()->subDays(30);

        if ($fromDate) {
            $query->where('created_at', '>', $fromDate);
        }

        $records = $query->get()->keyBy('day');

        // Process Data For Chart
        $days = [];
        $labels = [];
        $now = Carbon::now();

        while ($fromDate < $now) {
            $key = $fromDate->format('Y-m-d');
            $labels[] = $key;
            $fromDate = $fromDate->addDay(1);
            $days[] = isset($records[$key]) ? $records[$key]['count'] : 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $label,
                    'backgroundColor' => '#f87979',
                    'data' => $days
                ]
            ]
        ];
    }
}
