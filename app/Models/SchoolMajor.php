<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolMajor extends Model
{
    protected $fillable = ['school_id', 'name'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
