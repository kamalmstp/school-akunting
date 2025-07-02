<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StudentReceivables extends Model
{
    protected $fillable = ['student_id', 'account_id', 'school_id', 'amount', 'paid_amount', 'due_date', 'status', 'period', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->whereNull('deleted_at');
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student_receivable_details()
    {
        return $this->hasMany(StudentReceivableDetail::class, 'student_receivable_id');
    }
}
