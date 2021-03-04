<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanceledLog extends Model
{

    protected $fillable = [
        "member_id",
        "credit_id",
    ];

    public function member()
    {
        return $this->hasOne(Member::class, "id", "member_id");
    }



    /**
     * 解約時点で､一番直近で決済されたpayment_logデータを取得する
     *
     * @return void
     */
    public function payment_log()
    {
        return $this->hasOne(PaymentLog::class, "credit_id", "credit_id")->orderBy("paid_at", "desc");
    }
}
