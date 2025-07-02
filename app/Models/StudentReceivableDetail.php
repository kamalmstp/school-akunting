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
}
