<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class TransactionController extends Controller
{

    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService){
        $this->transactionService = $transactionService;
    }

    public function index (Request $request):Response{
        $request->validate([
            'per_page' => 'nullable|numeric|min:1',
            'page' => 'nullable|numeric|min:1',
            'search' => 'nullable|string|max:255'
        ]);
        $user = auth()->user();
        $transactions = $this->transactionService->getTransactionsByUser($user, $request, $request->per_page);

        $results = ['transactions'=> TransactionResource::collection($transactions)];

         if ($request->per_page) {
            $results['per_page'] = $transactions->perPage();
            $results['current_page'] = $transactions->currentPage();
            $results['last_page'] = $transactions-> lastPage();
            $results['total'] = $transactions->total();
        }
        return response([
            'message' => __('app.data_load_success',['data'=>__('app.transactions')]),
            'results' => $results
        ]);
    }

    public function get(Request $request, $id){
        $user = auth()->user();
        $transaction = $this->transactionService->getTransactionByUserUuid($user, $id);
        return response([
            'message' => __('app.data_load_success', ['data'=>__('app.transaction')]),
            'results' => [
                'transaction' => new TransactionResource($transaction)
            ]
        ]);
    }

    public function store(Request $request): Response
    {
         $request->validate(
            [
            'account' => 'required|exists:accounts,uuid',
            'category' => 'nullable|exists:categories,uuid',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'transaction_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:100',
            ]
        );
        $user = auth()->user();
        $transaction = $this->transactionService->create($user, $request);
        return response([
            'message'=> __('app.data_create_success', ['data' => __('app.transaction')]),
            'results' => [
                'transaction' => new TransactionResource($transaction)
            ]
        ]);
    }

    public function update (Request $request, $id){
         $request->validate(
            [
            'account' => 'required|exists:accounts,uuid',
            'category' => 'nullable|exists:categories,uuid',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'transaction_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:100',
            ]);


        $user = auth()->user();

        $transaction = $this->transactionService->getTransactionByUserUuid($user, $id);

        $transaction = $this->transactionService->update($transaction, $request);

        return response([
            'message' => __('app.data_update_success',['data'=>__('app.transaction')]),
            'results' => [
                'transaction' => new TransactionResource($transaction)
            ]
        ]);

    }

    public function delete(Request $request, $id){
        $user = auth()->user();

        $transaction = $this->transactionService->getTransactionByUserUuid($user, $id);

        $result = $this->transactionService->delete($transaction);

        if(!$result){
            abort(500, __('app.data_delete_error', ['data'=>__('app.transaction')]));
        }

        return response([
            'message' => __('app.data_delete_success',['data'=>__('app.transaction')])
        ]);

    }
}
