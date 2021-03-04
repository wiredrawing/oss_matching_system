<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailReset extends Model
{

    protected $attributes = [
        "is_used" => 0,
    ];

    protected $dates = [
        "expired_at"
    ];

    protected $fillable = [
        "member_id",
        "email",
        "token",
        "expired_at",
        "is_used",
    ];
}
