<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Teacher extends Model
{
    protected $fillable = ['school_id', 'name', 'teacher_id_number', 'nik', 'education', 'tmt', 'work_period', 'certification', 'employment_status', 'is_active', 'email', 'phone', 'address', 'deleted_at'];

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

    public function teacher_receivables()
    {
        return $this->hasMany(TeacherReceivable::class);
    }
}
