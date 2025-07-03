<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function getSampleAttribute():String{
        $value = number_format(1000,$this->decimal_places,$this->decimal_separator,$this->thousand_separator);

        return $this->symbol_position == 'after' ? $value . $this->symbol : $this->symbol . $value;
    }


}
