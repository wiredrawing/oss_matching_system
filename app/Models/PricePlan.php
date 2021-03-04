<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricePlan extends Model
{
    protected $attributes = [
        // 該当のプランを選択できるようにするかどうか?
        "is_displayed" => 1,
    ];

    protected $fillable = [
        "gender",
        "plan_code",
        "duration",
        "name",
        "price",
        "is_displayed",
    ];
}
