<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherReceivableDetail extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_receivable_id', 'description', 'amount', 'period', 'reason'];

    public function teacher_receivable()
    {
        return $this->belongsTo(TeacherReceivable::class, 'teacher_receivable_id');
    }

    /**
     * Scope untuk filter berdasarkan teacher_id, account_id, school_id
     */
    public function scopeFilterByTeacherAccountSchool($query, $teacherId, $accountId, $schoolId)
    {
        return $query->whereHas('teacher_receivable', function ($q) use ($teacherId, $accountId, $schoolId) {
            $q->where('teacher_id', $teacherId)
              ->where('account_id', $accountId)
              ->where('school_id', $schoolId);
        });
    }
}
