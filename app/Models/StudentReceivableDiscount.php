<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReceivableDiscount extends Model
{
    use HasFactory;

    protected $fillable = ['student_receivable_id', 'label', 'percent', 'nominal'];

    public function studentReceivable()
    {
        return $this->belongsTo(StudentReceivables::class, 'student_receivable_id');
    }
}
