<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->uuid,
            'account_type'=> new AccountTypeResource($this->account_type),
            'currency'=> new CurrencyResource($this->currency),
            'name'=>$this->name,
            'initial_balance'=>$this->initial_balance,
            'initial_balance_text' => $this->initial_balance_text,
            'current_balance' => $this->current_balance,
            'current_balance_text' => $this->current_balance_text,
            'colour_code' => $this->colour_code,
            'active'=> $this->active,
            'total_income' => $this->total_income,
            'total_income_text' =>$this->total_income_text,
            'total_expense' =>$this->total_expense,
            'total_expense_text' =>$this->total_expense_text,
            'transaction_count'=>$this->transaction_count,
            'income_count'=>$this->income_count,
            'expense_count'=>$this->expense_count,
            'account_summary' => $this->getSummary(),
            'balance_last_month' => $this->getBalanceAsOf('last_month'),

        ];
    }
}
