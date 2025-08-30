<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
            'transaction_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    // Accessor to get formatted type for display
    public function getFormattedTypeAttribute(): string{
        return ucfirst($this->type);
    }

     public function account():BelongsTo{
        return $this->belongsTo(Account::class,'account_id');
    }

    public function category():BelongsTo{
        return $this->belongsTo(Category::class, 'category_id');
    }

      public function getFormattedAmountTextAttribute():string{
       return $this->account->currency->format($this->amount);
    }

    // Transaction Attachments
   public function attachments()
{
    return $this->hasMany(TransactionAttachment::class);
}

/**
 * Override toArray to include attachments
 */
public function toArray()
{
    $array = parent::toArray();

    // Include attachments if loaded
    if ($this->relationLoaded('attachments')) {
        $array['attachments'] = $this->attachments->toArray();
    }

    return $array;
}

}
