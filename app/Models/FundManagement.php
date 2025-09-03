<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundManagement extends Model
{
    protected $fillable = ['school_id', 'account_id', 'name', 'amount'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
