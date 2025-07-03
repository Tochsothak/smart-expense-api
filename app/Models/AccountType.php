<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountType extends BaseModel
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'active'

    ];
}
