<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EmployeeReceivable extends Model
{
    protected $fillable = ['employee_id', 'account_id', 'school_id', 'amount', 'paid_amount', 'due_date', 'status', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->whereNull('deleted_at');
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function employee_receivable_details()
    {
        return $this->hasMany(EmployeeReceivableDetail::class, 'employee_receivable_id');
    }
}
