<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ViolationReport extends Model
{

    protected $fillable = [
        // 通報者
        "from_member_id",
        // 違反者
        "to_member_id",
        // 抵触内容
        "message",
    ];


    public function from_member()
    {
        return $this->hasOne(Member::class, "id", "from_member_id");
    }

    public function to_member()
    {
        return $this->hasOne(Member::class, "id", "to_member_id");
    }

    // 抵触した違反項目
    public function violation_categories()
    {
        return $this->hasMany(ViolationCategory::class);
    }
}
