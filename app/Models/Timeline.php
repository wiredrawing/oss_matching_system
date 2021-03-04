<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    // DBカラムの初期値
    protected $attributes = [
        "is_browsed" => 0,
    ];

    protected $fillable = [
        "from_member_id",
        "to_member_id",
        "timeline_type",
        "message_id",
        "url_id",
        "image_id",
        // 異性のユーザの閲覧フラグ
        "is_browsed",
    ];


    public function message()
    {
        return $this->hasOne(Message::class, "id", "message_id");
    }

    public function url()
    {
        return $this->hasOne(Url::class, "id", "url_id");
    }

    public function image()
    {
        return $this->hasOne(Image::class, "id", "image_id");
    }
}
