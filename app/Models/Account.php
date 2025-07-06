<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'account_type_id',
        'currency_id',
        'name',
        'initial_balance',
        'colour_code',
        'active'
    ];

    protected function casts() :array {
        return [
            'initial_balance' => 'double'
        ]
        ;
    }

    public function account_type():BelongsTo{
        return $this->belongsTo(AccountType::class,'account_type_id');
    }

    public function currency():BelongsTo{
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function getInitialBalanceTextAttribute(): String {
       return $this->currency->format($this->initial_balance + 0 );
    }

    public function getCurrentBalanceAttribute(): float{
        $value = $this->initial_balance + 0;

        return $value;
    }

    public function getCurrentBalanceTextAttribute():String{
        return $this->currency->format($this->current_balance);
    }
}
