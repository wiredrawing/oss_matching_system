<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footprint extends Model
{
    protected $fillable = [
        // アクセスしたメンバーID(来訪元)
        "from_member_id",
        // アクセスされたメンバーID(来本先)
        "to_member_id",
        "access_count",
        "is_browsed",
    ];



    // 足跡をつけたユーザー
    public function from_member ()
    {
        return $this->hasOne(Member::class, "id", "from_member_id");
    }

    // 足跡をつけられたユーザー
    public function to_member ()
    {
        return $this->hasOne(Member::class, "id", "to_member_id");
    }
}
