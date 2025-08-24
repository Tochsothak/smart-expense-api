<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRateModel extends Model
{
     protected $fillable = [
    'from_currency',
    'to_currency',
    'rate',
    'active',
    ];
}
