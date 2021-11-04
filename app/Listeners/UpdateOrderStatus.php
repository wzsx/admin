<?php

namespace App\Listeners;

use App\Events\OrderStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateOrderStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderStatus  $event
     * @return void
     */
    public function handle(OrderStatus $event)
    {
        //
    }
}
