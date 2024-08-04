<?php

namespace App\Http\patterns\builder;

use App\Http\Enum\OrderStatuesEnum;
use App\Models\orders_items;
use App\Models\payments;
use App\Services\Messages;

class RemoveOrderItemBuilder
{
    public $order_item;
    private $data;
    private $total_price_item = 0;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public  function init()
    {
        $allowed = [OrderStatuesEnum::pending->value,OrderStatuesEnum::review->value];
        $this->order_item = orders_items::query()->with(['order'=>function ($e) {
            $e->with('last_status','payment');
        },'properties'])->find($this->data['order_item_id']);
        // check if removed before
        if($this->order_item->is_cancelled != null){
            return Messages::error(__('errors.already_removed'));
        }
        // check that order in pending mode or review mode
        if(!(in_array($this->order_item->order->last_status->status,$allowed))){
            return Messages::error(__('errors.order_on_working_mode_you_cant_delete'));
        }

        return true;



    }

    public  function detect_full_cost()
    {
        //
        $base_cost = $this->order_item->price;
        foreach($this->order_item->properties as $property){
            $base_cost += $property->price;
        }
        $this->total_price_item = $base_cost * $this->order_item->paper_number * $this->order_item->copies_number;
        return $this;
    }

    public function cancel_item()
    {
        // remove this item
        $this->order_item->update([
            'is_cancelled'=>json_encode(['who'=>auth()->user()->roleName(),'reason'=>$this->data['reason']],JSON_UNESCAPED_UNICODE)
        ]);
        return $this;
    }

    public function handle_payment()
    {
        payments::query()->find($this->order_item->order->payment->id)->update([
            'money'=>$this->order_item->order->payment->money - $this->total_price_item
        ]);
        return Messages::success(__('messages.saved_successfully'));
    }

    public function cancel_order()
    {

    }
}
