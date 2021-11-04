<?php

namespace App\Jobs;

use App\Model\GoodsModel;
use App\Model\OrderGoodsModel;
use App\Model\OrderModel;
use App\Http\Controllers\Order\OrderController;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $order_no;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_no)
    {
        //
        $this->order_no = $order_no;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $user = $this->order_no;
//        GoodsModel::query()->where(['goods_id'=>$user])->update(['goods_name'=>'薄荷清茶']);
        $status = OrderModel::query()->where(['order_no'=>$user])->pluck('is_pay');
        if($status!=1){
            OrderModel::query()->where(['order_no'=>$user])->update(['status'=>0,'is_deleted'=>1]);
            OrderGoodsModel::query()->where(['order_no'=>$user])->update(['is_deleted'=>1]);
        }
    }
}