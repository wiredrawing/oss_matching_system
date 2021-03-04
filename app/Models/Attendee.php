<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{


    protected $primaryKey = [
        "room_id",
        "member_id",
    ];

    public $incrementing = false;

    protected $fillable = [
        "room_id",
        "member_id",
    ];
}
