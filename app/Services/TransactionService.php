<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionAttachment;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Storage;
use Str;

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

        $transaction =  Transaction::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'notes' => $request->notes ?? null,
            'transaction_date' => $request->transaction_date ?? Carbon::now()->setTimezone('Asia/Phnom_Penh'),
            'active' => 1,
            'reference_number' => $request->reference_number ?? '',
        ]);

       // Handle file uploads
            if ($request->hasFile('attachments')) {
                $this->handleFileUploads($transaction, $request->file('attachments'));
            }

            // Load the transaction with relationships
            $transaction->load(['account', 'category', 'attachments']);
        return $transaction;
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
        $transaction->transaction_date = $request->transaction_date ?? Carbon::now()->setTimezone('Asia/Phnom_Penh');
        $transaction->reference_number = $request->transaction->reference_number ?? null;
        $transaction->active = $request->active ?? 1;
        $transaction->updated_at = Carbon::now();
        $transaction->update();
  // Handle file deletions
            if ($request->filled('delete_attachments')) {
                $attachmentIds = explode(',', $request->delete_attachments);
                $this->deleteAttachments($transaction, $attachmentIds);
            }

            // Handle new file uploads
            if ($request->hasFile('new_attachments')) {
                $this->handleFileUploads($transaction, $request->file('new_attachments'));
            }

            // Load the transaction with relationships
            $transaction->load(['account', 'category', 'attachments']);
        return $transaction;

    }

    public function delete(Transaction $transaction){
        $transaction->delete();
        return true;
    }

    // Handle file upload
    private function handleFileUploads(Transaction $transaction, array $files)
    {
        foreach ($files as $file) {
            // Generate unique filename
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store file in storage/app/public/transaction-attachments
            $path = $file->storeAs('transaction-attachments', $filename, 'public');

            // Create attachment record
            TransactionAttachment::create([
                'transaction_id' => $transaction->id,
                'filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }


 
}
