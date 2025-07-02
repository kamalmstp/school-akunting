<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    protected $fillable = ['school_id', 'name', 'student_id_number', 'class', 'is_active', 'phone', 'address', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->whereNull('deleted_at');
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function receivables()
    {
        return $this->hasMany(StudentReceivables::class);
    }
}
