<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherReceivableDetail extends Model
{
    protected $fillable = ['teacher_receivable_id', 'description', 'amount', 'period', 'reason'];

    public function teacher_receivable()
    {
        return $this->belongsTo(TeacherReceivable::class, 'teacher_receivable_id');
    }
}
