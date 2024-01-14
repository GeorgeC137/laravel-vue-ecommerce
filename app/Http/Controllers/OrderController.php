<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::query()->where(['created_by' => $user->id])->orderBy('created_at', 'desc')->paginate(10);

        return view('order.index', compact('orders'));
    }

    public function view(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->created_by !== $user->id) {
            return response('Unauthorized Action!!!', 403);
        }

        return view('order.view', compact('order'));
    }
}
