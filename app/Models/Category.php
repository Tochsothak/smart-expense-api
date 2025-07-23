<?php

namespace App\Models;

class Category extends BaseModel
{
    protected $fillable = [
        'id',
        'name',
        'code',
        'description',
        'colour_code',
        'active',
    ];
}
