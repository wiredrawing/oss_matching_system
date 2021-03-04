<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalLog extends Model
{
    protected $fillable = [
        // アプリケーション側のmember_id
        "member_id",
        // テレコムクレジット側のID
        "credit_id",
        // 退会理由
        "withdrawal",
        // 退会時のメッセージ
        "opinion",
        // 退会日時
        "withdrawn_at"
    ];



    public function member()
    {
        return $this->hasOne(Member::class, "id", "member_id");
    }
}
