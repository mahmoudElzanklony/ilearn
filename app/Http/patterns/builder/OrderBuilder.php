<?php

namespace App\Http\patterns\builder;

use App\Actions\ImageModalSave;
use App\Actions\PaymentModalSave;
use App\Actions\ValidateCouponAction;
use App\Http\Enum\OrderStatuesEnum;
use App\Http\Resources\OrderResource;
use App\Models\images;
use App\Models\orders;
use App\Models\orders_coupons;
use App\Models\orders_items;
use App\Models\orders_items_properties;
use App\Models\orders_tracking;
use App\Models\payments;
use App\Models\properties;
use App\Models\services;
use App\Services\Messages;
use Illuminate\Support\Facades\DB;

class OrderBuilder
{

    private $base_order_info;
    private $items;
    private $coupon_number;
    private $payment;
    private $order;
    private $coupon = null;
    private $total_price_order = 0;
    private $payment_strategy;

    public function __construct($base_order_info , $items , $payment , $coupon_number = null , $payment_strategy)
    {
        $this->base_order_info = collect($base_order_info)->toArray();
        $this->items = $items;
        $this->coupon_number = $coupon_number;
        $this->payment = $payment;
        $this->payment_strategy = $payment_strategy;
    }

    public function initOrder()
    {
        DB::beginTransaction();
        if(array_key_exists('note',$this->base_order_info)){
            $this->base_order_info['note'] = json_encode(['system_refund'=>'','client'=>$this->base_order_info['note']],JSON_UNESCAPED_UNICODE);
        }
        // create new order
        $this->base_order_info['user_id'] = auth()->id();
        $this->order = orders::query()->create($this->base_order_info);
        return $this;
    }

    public function prepare_status()
    {
        orders_tracking::query()->create([
            'order_id'=>$this->order->id,
            'status'=>OrderStatuesEnum::pending
        ]);
        return $this;
    }
    public function save_items()
    {
        foreach($this->items as $item){
            $file = $this->check_upload_file($item['file']);
            $item['order_id'] = $this->order->id;
            $items_data = $this->prepare_order_item($item,$file);
            $order_item = orders_items::query()->create($items_data);
            // init properties price
            $total_properties_price = 0;
            // get properties that related to this order
            foreach($item['properties'] as $property){
                $property_item = $this->create_item_property($property,$order_item);
                $total_properties_price += $property_item->price;
            }
            $this->total_price_order += ($total_properties_price + $order_item->price) * $order_item->paper_number * $order_item->copies_number;
        }
        return $this;
    }

    public function validate_coupon()
    {
        if(isset($this->coupon_number)){
            $coupon_data =  ValidateCouponAction::validate($this->coupon_number);
            if(!(isset($coupon_data->original['errors']))){
                $coupon_final_price_with_coupon_value = ValidateCouponAction::detectFinalPrice($this->total_price_order , $coupon_data);
                $this->total_price_order = $coupon_final_price_with_coupon_value['final_price'];
                // save coupon related to this order
                $this->save_coupon_to_order($coupon_data->id,$coupon_final_price_with_coupon_value['coupon_value']);
            }
        }
        return $this;

    }

    public function save_coupon_to_order($coupon_id,$coupon_value)
    {
        orders_coupons::query()->create([
            'order_id'=>$this->order->id,
            'coupon_id'=>$coupon_id,
            'coupon_value'=>$coupon_value,
        ]);
    }

    public function save_payment()
    {
        $validate_payment = $this->payment_strategy->validate($this->total_price_order);
        if($validate_payment === true) {
            PaymentModalSave::make($this->order->id, 'orders', $this->total_price_order, $this->payment['type']);
            DB::commit();
            $this->order->load('items');
            $this->order->load('user');
            $this->order->load('payment');
            $this->order->load('coupon_info');
            return Messages::success(__('messages.saved_successfully'),OrderResource::make($this->order));
        }else{
            return $validate_payment;
        }
    }

    public  function check_upload_file($file)
    {
        $name = time().rand(0,9999999999999). '_file.' . $file->getClientOriginalExtension();
        $file->move(public_path('orders_files/'), $name);
        return $name;
    }

    public function prepare_order_item($item,$file)
    {
        $items_data = collect($item)->except('properties','file');
        $items_data = collect($items_data)->toArray();
        $items_data['file'] = $file;
        $items_data['price'] = services::query()->find($items_data['service_id'])->price;
        return $items_data;
    }

    public function create_item_property($property,$order_item)
    {
        $property_item = properties::query()->find($property['property_id']);

        $property_obj = orders_items_properties::query()->create([
            'order_item_id'=>$order_item->id,
            'property_id'=>$property['property_id'],
            'price'=>$property_item->price,
        ]);
        return $property_obj;
    }
}
