<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        // アクション元ID
        "from_member_id",
        // アクション先ID
        "to_member_id",
        // アクション内容
        "action_id",
        // 閲覧フラグ
        "is_browsed",
    ];

    // 追加の動的プロパティ
    protected $appends = [
        "url",
    ];

    // from_member_id
    public function from_member () {
        return $this->belongsTo(Member::class, "from_member_id");
    }

    // to_member_id
    public function to_member()
    {
        return $this->belongsTo(Member::class, "to_member_id");
    }

    public function getUrlAttribute()
    {
        if ($this->action_id === Config("const.action.message")) {
            return action("Member\\MessageController@talk", [
                "to_member_id" => $this->from_member->id,
            ]);
        } else {
            return action("Member\\IndexController@opponent", [
                "target_member_id" => $this->from_member->id,
            ]);
        }
    }
}
