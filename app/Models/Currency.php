<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends BaseModel
{
    use HasFactory;

    protected $fillable = [
    'name',
    'code',
    'symbol',
    'symbol_position',
    'thousand_separator',
    'decimal_separator',
    'decimal_places',
    'active',
    ];

    public function account():HasMany{
        return $this->hasMany(Account::class);
    }



    public function getSampleAttribute():String{
       return $this->format(1000) ;
    }

    // Format normal number to currency
     public function format($value):String{
        $value = number_format($value, $this->decimal_places, $this->decimal_separator, $this->thousand_separator);
        return ($this->symbol_position == 'after')
        ? ($value . ' ' . $this->symbol ?? 'USD')
        : ($this->symbol .' ' . $value);
    }
}
