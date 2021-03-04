<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdministratorLog extends Model
{
    use SoftDeletes;

    // DBの初期値を設定
    protected $attributes = [
        "login" => 0,
        "logout" => 0,
    ];

    protected $fillable = [
        "administrator_id",
        "login",
        "logout",
        "http_user_agent"
    ];

    public function administrator ()
    {
        return $this->hasOne(Administrator::class, "administrator_id");
    }
}
