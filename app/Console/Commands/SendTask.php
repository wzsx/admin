<?php

namespace App\Console\Commands;

use App\Model\OrderModel;
use Illuminate\Console\Command;

class SendTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:task';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $order_no = OrderModel::query()->where(['status'=>3])->select('order_no')->get()->toArray();
        if($order_no){
            OrderModel::query()->whereIn('order_no',$order_no)->where('shipments_at','<=',date('Y-m-d H:i:s', strtotime('-15days')))->update(['status'=>4,'complete_date'=>date('Y-m-d H:i:s')]);
        }
    }
}
