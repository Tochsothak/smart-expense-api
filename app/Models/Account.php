<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends BaseModel
{
    // use SoftDeletes;
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
            'initial_balance' => 'decimal:2'
        ];
    }
    // Relationships
    public function user():BelongsTo {
        return $this->belongsTo(User::class);
    }
    public function account_type():BelongsTo{
        return $this->belongsTo(AccountType::class,'account_type_id');
    }

    public function currency():BelongsTo{
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function transactions():HasMany{
        return $this->hasMany(Transaction::class);
    }

    public function activeTransactions():HasMany{
        return $this->hasMany(Transaction::class)
        ->where('active', 1);
    }

    //Scope
    public function scopeActive($query){
        return $query->where('active', 1);
    }

    // Balance Calculation(Original Currency)
    public function getCurrentBalanceAttribute(): float{
       $incomeTotal = $this->activeTransactions()
       ->where('type','income')
       ->sum('amount');

       $expenseTotal = $this
       ->activeTransactions()
       ->where('type', 'expense')
       ->sum('amount');

       return $this->initial_balance + $incomeTotal - $expenseTotal;
    }

    public function getIncomeCountAttribute():int{
        return $this->activeTransactions()
        ->where( 'type', 'income')
        ->count();
    }

    public function getExpenseCountAttribute():int{
        return $this->activeTransactions()
        ->where('type','expense')
        ->count();
    }

    public function getTransactionCountAttribute(){
        return $this->activeTransactions()
        ->count();
    }
    public function getTotalIncomeAttribute():float{
        return $this->activeTransactions()
        ->where('type', 'income')
        ->sum('amount');
    }

    public function getTotalExpenseAttribute():float{
        return $this->activeTransactions()
        ->where('type', 'expense')
        ->sum('amount');
    }

    // Currency Conversion Methods
    public function getCurrentBalanceInCurrency(string $targetCurrencyCode){
        $currentBalance = $this->current_balance;
        if($this->currency->code === $targetCurrencyCode){
            return $currentBalance;
        }
        return  app(CurrencyConversionService::class)
                ->convert($currentBalance, $this->currency->code, $targetCurrencyCode);
    }

    public function getInitialBalanceInCurrency(string $targetCurrencyCode) {
        $initialBalance = $this->initial_balance;
        if ($this->currency->code === $targetCurrencyCode){
            return $initialBalance;
        }
        return app(CurrencyConversionservice::class)
                ->convert($initialBalance, $this->currency->code, $targetCurrencyCode);
    }

    public function getTotalIncomeInCurrency(string $targetCurrencyCode){
        $totalIncome = $this->total_income;
        if ($this->currency->code === $targetCurrencyCode){
            return $totalIncome;
        }
        return app(CurrencyConversionService::class)
                ->convert($totalIncome, $this->currency->code, $targetCurrencyCode);
    }

    public function getTotalExpenseInCurrency(string $targetCurrencyCode){
        $totalExpense = $this->total_expense;
        if($this->currency->code === $targetCurrencyCode){
            return $totalExpense;
        }
        return app(CurrencyConversionService::class)
            ->convert($totalExpense, $this->currency->code, $targetCurrencyCode);
    }

      // Helper method for balance calculations
    public function getBalanceAsOf($date) {
        $incomeTotal = $this->activeTransactions()
        ->where('type', 'income')
        ->where('transaction_date', '<=', $date)
        ->sum('amount');

        $expenseTotal = $this->activeTransactions()
        ->where('type', 'expense')
        ->where('transaction_date', '<=', $date)
        ->sum('amount');

        return $this->initial_balance + $incomeTotal - $expenseTotal;
    }

    // Formatted text attribute(Original currency)
     public function getInitialBalanceTextAttribute(): String {
       return $this->currency->format($this->initial_balance);
    }
    public function getCurrentBalanceTextAttribute():String{
        return $this->currency->format($this->current_balance);
    }

    public function getTotalExpenseTextAttribute(){
        return $this->currency->format($this->total_expense);
    }

    public function getTotalIncomeTextAttribute():string{
        return  $this->currency->format($this->total_income);
    }

    public function getBalanceAsOfText($date):string {
        return $this->currency->format($this->getBalanceAsOf($date));
    }

    // Formatted text Attribute (Converted Currency)
    public function getCurrentBalanceInTextInCurrency (string $targetCurrencyCode):string{
        $convertedAmount = $this->getCurrentBalanceInCurrency($targetCurrencyCode);
        $targetCurrency = Currency::where('code', $targetCurrencyCode)->first();
        return $targetCurrency ? $targetCurrency
        ->format($convertedAmount)
        : number_format($convertedAmount, 2);
    }

    public function getInitialBalanceTextInCurrency(string $targetCurrencyCode){
        $convertedAmount = $this->getInitialBalanceIncurrency();
        $targetCurrency = Currency::where('code', $targetCurrencyCode)->first();
        return $targetCurrency ? $targetCurrency
        ->format($convertedAmount)
        : number_format($convertedAmount);
    }

        // Check if account has sufficient balance for expense
    public function hasSufficientBalance($amount){
        return $this->current_balance >= $amount;
    }

   // Get Account Summary
    public function getSummary():array {
        return [
            'initial_balance' =>  $this->initial_balance,
            'total_income' => $this->total_income,
            'total_expense'=> $this->total_expense,
            'current_balance'=> $this->current_balance,
            'transaction_count'=>$this->transaction_count,
            'income_count'=>$this->income_count,
            'expense_count'=>$this->expense_count,
        ];
    }

}
