<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    public $table = "payment_logs";

    protected $attributes = [
        "canceled_at" => NULL,
    ];

    protected $dates = [
        "paid_at",
        "canceled_at",
    ];

    protected $fillable = [
        "member_id",
        "credit_id",
        // クレジット会社側課金タイプ
        "cont",
        "email",
        "money",
        "plan_code",
        // 決済成功有無
        "rel",
        "settle_count",
        "telno",
        "user_name",
        "paid_at",
        "canceled_at",
    ];

    /**
     * 支払い元ユーザー
     *
     * @return void
     */
    public function member() {
        return $this->belongsTo(Member::class, "member_id");
    }

    /**
     * 有料プラン情報
     *
     * @return void
     */
    public function price_plan()
    {
        return $this->belongsTo(PricePlan::class, "plan_code", "plan_code");
    }
}
