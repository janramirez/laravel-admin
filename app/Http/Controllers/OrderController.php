<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::paginate();

        return OrderResource::collection($order);
    }

    public function show($id)
    {
        return new OrderResource(Order::find($id));
    }
}
