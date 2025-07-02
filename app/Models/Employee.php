<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Model
{
    protected $fillable = ['school_id', 'name', 'employee_id_number', 'is_active', 'email', 'phone', 'address', 'deleted_at'];

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

    public function employee_receivables()
    {
        return $this->hasMany(EmployeeReceivable::class);
    }
}
