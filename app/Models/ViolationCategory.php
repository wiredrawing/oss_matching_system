<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ViolationCategory extends Model
{

    protected $fillable = [
        "violation_report_id",
        "category_id",
    ];


    public function violation_report()
    {
        return $this->belongsTo(ViolationReport::class, "violation_report_id", "id");
    }
}
