<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReceivableDetail extends Model
{
    use HasFactory;

    protected $fillable = ['student_receivable_id', 'description', 'amount', 'period', 'reason'];

    public function student_receivable()
    {
        return $this->belongsTo(StudentReceivables::class, 'student_receivable_id');
    }

    /**
     * Scope untuk filter berdasarkan student_id, account_id, school_id
     */
    public function scopeFilterByStudentAccountSchool($query, $studentId, $accountId, $schoolId)
    {
        return $query->whereHas('student_receivable', function ($q) use ($studentId, $accountId, $schoolId) {
            $q->where('student_id', $studentId)
              ->where('account_id', $accountId)
              ->where('school_id', $schoolId);
        });
    }
}
