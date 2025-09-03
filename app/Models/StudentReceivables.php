<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudentReceivables extends Model
{
    protected $fillable = ['student_id', 'account_id', 'school_id', 'amount', 'paid_amount', 'due_date', 'total_discount', 'infaq', 'total_payable', 'status', 'period', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->whereNull('student_receivables.deleted_at');
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

    public function discounts()
    {
        return $this->hasMany(StudentReceivableDiscount::class, 'student_receivable_id');
    }

    public static function getPaidAmountCounter($school_id = '', $account_name = '')
    {
        return self::join('accounts as a', 'a.id', '=', 'student_receivables.account_id')
            ->select(
                'student_receivables.school_id',
                'a.name as account_name',
                DB::raw('COUNT(*) as total_records'),
                DB::raw('SUM(student_receivables.paid_amount) as total_paid_amount')
            )
            ->whereIn('student_receivables.status', ['Paid', 'Partial'])
            ->when($school_id, function ($q) use ($school_id) {
                $q->where('student_receivables.school_id', $school_id);
            })
            ->when($account_name, function ($q) use ($account_name) {
                $q->where('a.name', 'like', '%' . $account_name . '%');
            })
            ->groupBy('student_receivables.school_id','a.name')
            ->get();
    }
}
