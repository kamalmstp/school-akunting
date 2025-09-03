<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    protected $fillable = ['school_id', 'account_id', 'fund_management_id', 'doc_number', 'date', 'description', 'debit', 'credit', 'reference_id', 'reference_type', 'payment_method', 'deleted_at', 'type'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->whereNull('transactions.deleted_at');
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function fund_management()
    {
        return $this->belongsTo(FundManagement::class);
    }
}
