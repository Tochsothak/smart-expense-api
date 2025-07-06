<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountTypeResource;
use Illuminate\Http\Response;
use App\Services\AccountTypeService;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    protected AccountTypeService $accountTypeService;

    public function __construct(AccountTypeService $accountTypeService){
        $this->accountTypeService = $accountTypeService;
    }

    public function index(Request $request): Response{
         $request->validate([
            'per_page' => 'nullable|numeric|min:1',
            'page' => 'nullable|numeric|min:1',
            'search' => 'nullable|string|max:255'

        ]);
        $accountType = $this->accountTypeService->getAll($request, $request->per_page);

        $results =  ['account_types' => AccountTypeResource::collection($accountType)];

        // Handle pagination
        if ($request->per_page) {
            $results['per_page'] = $accountType->perPage();
            $results['current_page'] = $accountType->currentPage();
            $results['last_page'] = $accountType-> lastPage();
            $results['total'] = $accountType->total();
        }

        //return
        return response ([
            'message' => __('app.data_load_success', ['data' => __('app.account_types')]),
            'results' => $results,
        ]);
    }

    public function get (Request $request, string $uuid):Response{

        $accountType = $this->accountTypeService->getByUuid($uuid);
        return response ([
            'message' => __('app.data_load_success', ['data' => __('app.account_type')]),
            'results' =>[
                'accountType' => new AccountTypeResource($accountType)
            ]
        ]);
    }
}
