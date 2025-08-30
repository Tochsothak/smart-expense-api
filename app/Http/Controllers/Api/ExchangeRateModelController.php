<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ExchangeRateResource;

use App\Services\CurrencyConversionService;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExchangeRateModelController extends Controller
{

    protected CurrencyConversionService $currencyConversion;

    public function __construct(CurrencyConversionService $currencyConversion){
        $this->currencyConversion = $currencyConversion;

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request ): Response
    {
        $request->validate([
            'amount'       => 'required|numeric',
            'from_currency' => 'required|string|size:3',
            'to_currency'   => 'required|string|size:3',
        ]);

        $result = $this->currencyConversion->convert(
           $request->amount,
           $request->from_currency,
           $request->to_currency
        );
        //
        return response ([
            'message' => __('app.data_load_success', ['data' => __('app.exchange_rate')]),
            'results' => [
                'exchange_rate' => [
                    'amount' => $request->amount,
                    'from' => $request->from_currency,
                    'to' => $request->to_currency,
                    'rate' => $result
                ]
            ]
        ]);
    }
}
