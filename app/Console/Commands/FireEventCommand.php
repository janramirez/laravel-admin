<?php

namespace App\Console\Commands;

use App\Jobs\AdminAdded;
use App\Jobs\OrderCompleted;
use App\Models\Order;
use Illuminate\Console\Command;

class FireEventCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $order = Order::find(1);

        $data = $order->toArray();
        $data['admin_total'] = $order->admin_total;
        $data['influencer_total'] = $order->influencer_total;

        OrderCompleted::dispatch($data);
    }
}
