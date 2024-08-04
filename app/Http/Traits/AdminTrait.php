<?php

namespace App\Http\Traits;

use App\Models\User;

trait AdminTrait
{
    public function currentAdmin()
    {
        return User::query()->role('admin','sanctum')->first();

    }
}
