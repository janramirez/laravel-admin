<?php

namespace App\Http\Controllers\Influencer;

use App\Models\Link;
use App\Models\Order;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class StatsController
{
    /**
     *  @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    public function index(Request $request)
    {
        $user = $this->userService->getUser();

        $links = Link::where('user_id', $user->id)->get();

        return $links->map(function(Link $link) {
            $orders = Order::where('code', $link->code)
              ->where('complete', 1)
              ->get();

            return [
                'code' => $link->code,
                'count' => $orders->count(),
                'revenue' => $orders->sum(function(Order $order) {
                    return $order->influencer_total;
                }),
            ];
        });
    }

    public function rankings()
    {
        $users = collect($this->userService->all(-1));

        $users = $users->filter(function ($user) {
            return $user->is_influencer;
        });

        $rankings = $users->map( function ($user) {
            $orders = Order::where('user_id', $user->id)->where('complete', 1)->get();

            return [
                'name' => $user->fullName(),
                'revenue' => $orders->sum(function (Order $order) {
                    return (int) $order->influencer_total;
                }),
            ];
        });

        return $rankings->sortByDesc('revenue')->values();
    }
}
