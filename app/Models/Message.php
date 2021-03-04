<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        "member_id",
        "message",
    ];


    public function member ()
    {
        return $this->belongsTo(Member::class, "member_id");
    }
}
