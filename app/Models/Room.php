<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    // プライマリキーをカスタマイズ
    protected $primaryKey = "id";
    public $incrementing = false;
    protected $keyType = "string";

    // room.id はランダムな255文字形式
    protected $fillable = [
        "id",
        "female_member_id",
        "male_member_id",
    ];
}
