<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'account_id',
        'financial_period_id',
        'amount',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function financialPeriod()
    {
        return $this->belongsTo(FinancialPeriod::class);
    }
}
