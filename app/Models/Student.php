<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    protected $fillable = ['school_id', 'name', 'student_id_number', 'national_student_number', 'year', 'parent_name', 'parent_phone', 'parent_mail', 'parent_job', 'class', 'phone', 'address', 'is_active', 'is_alumni', 'graduation_year', 'notes', 'certificate_status', 'deleted_at'];

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
