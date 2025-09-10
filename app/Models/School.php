<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class School extends Model
{
    protected $fillable = ['name', 'address', 'email', 'phone', 'logo', 'status', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->whereNull('deleted_at');
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
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

    public function cashManagements()
    {
        return $this->hasMany(CashManagement::class);
    }
}