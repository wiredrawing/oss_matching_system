<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberLog extends Model
{
    use SoftDeletes;

    // DBの初期値を設定
    protected $attributes = [
        "login" => 0,
        "logout" => 0,
    ];

    protected $fillable = [
        "member_id",
        "login",
        "logout",
        "http_user_agent"
    ];

    public function member ()
    {
        return $this->hasOne(Member::class, "member_id");
    }
}
