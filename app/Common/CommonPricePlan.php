<?php

namespace App\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\BaseMemberRequest;
use App\Models\PricePlan;

class CommonPricePlan
{


    /**
     * 現在､契約可能な有効な契約プランを取得
     *
     * @param integer $is_displayed
     * @return void
     */
    public static function getPricePlan(bool $is_displayed = false): \Illuminate\Database\Eloquent\Collection
    {
        if ($is_displayed === true) {
            $price_plans = PricePlan::where("is_displayed", Config("const.binary_type.on"))->get();
        } else {
            $price_plans = PricePlan::get();
        }
        return $price_plans;
    }
}
