<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Account extends Model
{
    protected $fillable = ['school_id', 'code', 'name', 'parent_id', 'account_type', 'normal_balance', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->whereNull('accounts.deleted_at');
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function studentReceivables()
    {
        return $this->hasMany(StudentReceivables::class);
    }

    public function fixedAssets()
    {
        return $this->hasMany(FixAsset::class);
    }

    public function depreciations()
    {
        return $this->hasMany(Depreciation::class);
    }
}
