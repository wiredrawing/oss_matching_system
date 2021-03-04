<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        // GoodしたメンバーID
        "from_member_id",
        // GoodされたメンバーID
        "to_member_id",
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

    public function images()
    {
        return $this->hasMany(Image::class, "member_id", "to_member_id");
    }
}
