<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Decline extends Model
{
    protected $fillable = [
        // 拒否アクションを起こしたメンバーID(ブロックした人)
        "from_member_id",
        // 拒否されたメンバーID(ブロックされた人)
        "to_member_id",
    ];


    public function to_member ()
    {
        return $this->belongsTo(Member::class, "to_member_id", "id");
    }

    public function from_member()
    {
        return $this->belongsTo(Member::class, "from_member_id", "id");
    }
}
