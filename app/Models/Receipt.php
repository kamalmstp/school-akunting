<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Receipt extends Model
{
    protected $fillable = [
        'school_id',
        'student_id',
        'invoice_no',
        'token',
        'date',
        'total_amount',
        'amount_words',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}