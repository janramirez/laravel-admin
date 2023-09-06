<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class UpdateRankingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:rankings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update ranking';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userService = new UserService();

        $users = collect($userService->all(-1));

        $users = $users->filter(function ($user) {
            return $user->is_influencer;
        });

        $users->each(function($user){
            $orders = Order::where('user_id', $user->id)
              ->where('complete', 1)
              ->get();

            $revenue = $orders->sum(function(Order $order) {
                return (int) $order->influencer_total;
            });

            Redis::zadd('rankings', $revenue, $user->first_name . ' ' . $user->last_name);
        });
    }
}
