<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class CashManagement extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'account_id', 'financial_period_id', 'name', 'amount'];

    protected $appends = ['balance'];

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

    protected function balance(): Attribute
    {
        return Attribute::make(
            get: function () {
                $activeFinancialPeriod = FinancialPeriod::getActive($this->school_id);
                if (!$activeFinancialPeriod) {
                    return 0;
                }
                
                $initialBalance = InitialBalance::where('account_id', $this->account_id)
                                                ->where('school_id', $this->school_id)
                                                ->where('financial_period_id', $activeFinancialPeriod->id)
                                                ->first();
                $initialAmount = $initialBalance ? $initialBalance->amount : 0;
                
                $totalDebit = DB::table('transactions')
                                ->where('account_id', $this->account_id)
                                ->where('school_id', $this->school_id)
                                ->whereBetween('date', [$activeFinancialPeriod->start_date, $activeFinancialPeriod->end_date])
                                ->sum('debit');

                $totalCredit = DB::table('transactions')
                                ->where('account_id', $this->account_id)
                                ->where('school_id', $this->school_id)
                                ->whereBetween('date', [$activeFinancialPeriod->start_date, $activeFinancialPeriod->end_date])
                                ->sum('credit');

                return $initialAmount + $totalDebit - $totalCredit;
            }
        );
    }
}
