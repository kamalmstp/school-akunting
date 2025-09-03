<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeReceivableDetail extends Model
{
    use HasFactory;

    protected $fillable = ['employee_receivable_id', 'description', 'amount', 'period', 'reason'];

    public function employee_receivable()
    {
        return $this->belongsTo(EmployeeReceivable::class, 'employee_receivable_id');
    }

    /**
     * Scope untuk filter berdasarkan employee_id, account_id, school_id
     */
    public function scopeFilterByEmployeeAccountSchool($query, $employeeId, $accountId, $schoolId)
    {
        return $query->whereHas('employee_receivable', function ($q) use ($employeeId, $accountId, $schoolId) {
            $q->where('employee_id', $employeeId)
              ->where('account_id', $accountId)
              ->where('school_id', $schoolId);
        });
    }
}
