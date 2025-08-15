<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends BaseModel
{
        protected $fillable = [
        'id',
        'user_id',
        'account_id',
        'category_id',
        'description',
        'notes',
        'amount',
        'type',
        'transaction_date',
        'reference_number',
        'active',
    ];

    protected function casts ():array {
        return [
            'amount' => 'double',
            'transaction_date' => 'datetime'
        ];
    }

    // Accessor to get formatted type for display
    public function getFormattedTypeAttribute(): string{
        return ucfirst($this->type);
    }

    // Alternative accessor with custom formatting
    // public function getTypeDisplayAttribute(){
    //     return match($this->type){
    //         'income' => 'Income',
    //         'expense' => 'Expense',
    //         default => ucfirst($this->type),
    //     };
    // }

    // public static function getTypeOptions(){
    //     return [
    //         'income'=> 'Income',
    //         'expense'=> 'Expense'
    //     ];
    // }

    // public static function getFormattedType($type){
    //     return self::getTypeOptions()[$type] ?? ucfirst($type);
    // }

     public function account():BelongsTo{
        return $this->belongsTo(Account::class,'account_id');
    }

    public function category():BelongsTo{
        return $this->belongsTo(Category::class, 'category_id');
    }

      public function getFormattedAmountTextAttribute():string{
       return $this->account->currency->format($this->amount);
    }

}
