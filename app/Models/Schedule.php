<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['school_id', 'account_id', 'income_account_id', 'user_type', 'description', 'amount', 'status', 'due_date', 'schedule_type'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function income_account()
    {
        return $this->belongsTo(Account::class, 'income_account_id');
    }
}
