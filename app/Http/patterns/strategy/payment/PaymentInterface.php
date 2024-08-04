<?php

namespace App\Http\patterns\strategy\payment;

interface PaymentInterface
{
    public function validate($price);
}
