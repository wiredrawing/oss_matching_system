<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Image extends Model
{
    // 論理削除
    use SoftDeletes;

    protected $attributes = [
        "blur_level" => 50,
        "is_approved" => 0,
    ];

    // 独自プロパティを追加付与
    protected $appends = [
        "image_url",
        "image_thumbnail_url",
    ];

    protected $fillable = [
        "member_id",
        "use_type",
        "filename",
        // URL参照時に必要なトークン
        "token",
        // 画像のぼかしレベル
        "blur_level",
        "is_approved",
    ];

    public function getImageUrlAttribute()
    {
        // 画像閲覧のためのURLを返す
        $image_url = action("Api\\v1\\MediaController@show",[
            "image_id" => $this->id,
            "token" => $this->token,
            "width" => Config("const.image.max_width"),
        ]);
        return $image_url;
    }

    public function getImageThumbnailUrlAttribute()
    {
        // 画像閲覧のためのURLを返す
        $image_url = action("Api\\v1\\MediaController@show",[
            "image_id" => $this->id,
            "token" => $this->token,
            "width" => Config("const.image.thumbnail_width"),
        ]);
        return $image_url;
    }
}
