<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\ChartResource;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;

class DashboardController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function chart()
    {
        $this->userService->allows('view', 'orders');
        
        $orders = Order::query()
         ->join('order_items', 'orders.id', '=', 'order_items.order_id')
         ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m-%d') as date, sum(order_items.quantity*order_items.price) as sum")
         ->groupBy('date')
         ->get();

        return ChartResource::collection($orders);
    }
}
