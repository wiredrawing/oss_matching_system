<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReissue extends Model
{
    protected $attributes = [
        "is_used" => 0,
    ];

    protected $dates = [
        "expired_at",
    ];

    protected $fillable = [
        "member_id",
        "token",
        "expired_at",
        "is_used",
    ];

    public function member ()
    {
        return $this->hasOne(Member::class, "id", "member_id");
    }
}
