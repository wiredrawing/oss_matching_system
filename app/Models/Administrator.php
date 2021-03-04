<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Administrator extends Model
{
    use SoftDeletes;

    // DBの初期値を設定
    protected $attributes = [
        "is_displayed" => 1,
        "permission_level" => 1,
    ];

    protected $dates = [
        "last_login",
    ];

    protected $fillable = [
        "email",
        "password",
        "display_name",
        "is_displayed",
        "permission_level",
        "last_login",
    ];
}
