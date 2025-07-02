<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeReceivableDetail extends Model
{
    protected $fillable = ['employee_receivable_id', 'description', 'amount', 'period', 'reason'];

    public function employee_receivable()
    {
        return $this->belongsTo(EmployeeReceivable::class, 'employee_receivable_id');
    }
}
