<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TransactionService{

    public function getTransactionsByUser(User $user, object $request, ?int $pagination = null):Collection|LengthAwarePaginator{
        $transactions = Transaction::where('user_id',$user->id)->orderBy('amount');

        if($request->search){
            $search = $request->search;
            $transactions->where('amount', 'LIKE', "%{$search}%"
            )->orWhere('type', 'LIKE', "{$search}");
        }
        return $pagination ? $transactions->paginate($pagination) : $transactions->latest()->get();
    }


    public function create(User $user, object $request): Transaction{

        $account = Account::where(['uuid'=>$request->account, 'active'=>1])->first();
        if(!$account){
            abort(404, __('app.data_not_found', ['data'=> __('app.account')]));
        }

        $category = Category::where(['uuid'=> $request->category, 'active' =>1 ])->first();
        if(!$category){
            abort(404, __('app.data_not_found', ['data' => __('app.category')]));
        }

        return Transaction::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'notes' => $request->notes ?? null,
            'transaction_date' => $request->transaction_date ?? Carbon::now(),
            'active' => 1,
            'reference_number' => $request->reference_number ?? null
        ]);
    }

    public function getTransactionByUserUuid(User $user, string $uuid):Transaction{
        $transaction = Transaction::where(['uuid'=>$uuid, 'user_id'=>$user->id,])->first();

        if(!$transaction){
            abort(404, __('app.data_not_found',['data'=>__('app.transaction')]));
        }
        return $transaction;
    }

    public function update(Transaction $transaction, object $request): Transaction{
        $account = Account::where(['uuid'=>$request->account, 'active'=>1])->first();

        if(!$account){
            abort(404, __('app.data_not_found',['data'=>__('app.account')]));
        }

        $category = Category::where(['uuid'=>$request->category, 'active' => 1])->first();
        if(!$category){
            abort(404, __('app.data_not_found',['data'=>__('app.category')]));
        }

        $transaction->account_id = $account->id;
        $transaction->category_id = $category->id;
        $transaction->description = $request->description;
        $transaction->notes = $request->notes;
        $transaction->amount = $request->amount;
        $transaction->type = $request->type;
        $transaction->transaction_date = $request->transaction_date ?? Carbon::now();
        $transaction->reference_number = $request->transaction->reference_number ?? null;
        $transaction->active = $request->active;
        $transaction->updated_at = Carbon::now();
        $transaction->update();

        return $transaction;

    }

    public function delete(Transaction $transaction){
        $transaction->delete();
        return true;
    }
}
