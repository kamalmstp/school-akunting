<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FixAsset extends Model
{
    protected $fillable = ['school_id', 'account_id', 'name', 'acquisition_date', 'acquisition_cost', 'useful_life', 'accumulated_depreciation', 'depreciation_percentage', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->whereNull('deleted_at');
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

    public function depreciations()
    {
        return $this->hasMany(Depreciation::class);
    }
}
