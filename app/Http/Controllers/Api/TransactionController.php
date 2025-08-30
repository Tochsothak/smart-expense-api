<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\TransactionAttachment;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Storage;


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
            'attachments' => 'nullable|array|max:5', // Max 5 files
            'attachments.*' => 'file|mimes:jpeg,jpg,png,pdf,doc,docx,txt|max:10240', // Max 10MB per file
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
            'new_attachments' => 'nullable|array|max:5',
            'new_attachments.*' => 'file|mimes:jpeg,jpg,png,pdf,doc,docx,txt|max:10240',
            'delete_attachments' => 'nullable|string', // Comma-separated attachment IDs
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


 public function downloadAttachment($transactionId, $attachmentId)
    {
        try {
            $user = auth()->user();
            // Find transaction by UUID
            $transaction = Transaction::where('uuid', $transactionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }
            // Find attachment by UUID
            $attachment = TransactionAttachment::where('uuid', $attachmentId)
                ->where('transaction_id', $transaction->id)
                ->first();
            if (!$attachment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attachment not found'
                ], 404);
            }
            // Check if file exists
            $filePath = storage_path('app/public/' . $attachment->file_path);
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found on server'
                ], 404);
            }
            // Return file directly
            return response()->file($filePath, [
                'Content-Disposition' => 'inline; filename="' . $attachment->filename . '"',
                'Content-Type' => $attachment->mime_type
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in downloadAttachment', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
                'attachment_id' => $attachmentId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving file: ' . $e->getMessage()
            ], 500);
        }
    }

 public function deleteAttachment($transactionId, $attachmentId)
    {
        try {
            $user = auth()->user();

            // Find transaction by UUID
            $transaction = Transaction::where('uuid', $transactionId)
                ->where('user_id', $user->id)
                ->first();
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }
            // Find attachment by UUID
            $attachment = TransactionAttachment::where('uuid', $attachmentId)
                ->where('transaction_id', $transaction->id)
                ->first();
            if (!$attachment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attachment not found'
                ], 404);
            }
            // Delete file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            // Delete attachment record
            $attachment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in deleteAttachment', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
                'attachment_id' => $attachmentId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attachment: ' . $e->getMessage()
            ], 500);
        }
    }
}
