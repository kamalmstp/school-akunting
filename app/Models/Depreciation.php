<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depreciation extends Model
{
    protected $fillable = ['fix_asset_id', 'account_id', 'date', 'amount', 'description', 'balance'];

    public function fixedAsset()
    {
        return $this->belongsTo(FixAsset::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
