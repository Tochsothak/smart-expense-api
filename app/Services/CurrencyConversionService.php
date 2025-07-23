<?php

namespace App\Servives;

class CurrencyConversionService {
    protected string $baseCurrency;
    protected int $cacheMinutes;

    public function __construct(){
        $this->baseCurrency  = config('app.base_currency', 'USD');
        $this->cacheMinutes = config('app.exchange_rate_cache_minutes', 60);
    }

    // Convert amount from one currency to another
    public function convert(float $amount, string $fromCurrency, string $toCurrency):float {
        // Same currency, no conversion need
        if($fromCurrency === $toCurrency){
            return $amount;
        }
        $exchangeRate = $this->getExchangeRate($fromCurrency, $toCurrency);
        return round($amount * $exchangeRate, 2);
    }

    // Get exchange rate between two currency
    public function getExchangeRate(string $fromCurrency, string $toCurrency):float {
        if ($fromCurrency === $toCurrency){
            return 1.0;
        }
        $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}";
        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($fromCurrency, $toCurrency){
            return $this->fetchExchangeRate($fromCurrency, $toCurrency);
        });
    }

    // Fetch exchange rate from database or external API
    protected function fetchExchangeRate(string $fromCurrency, string $toCurrency){
        //Try to get from database first
        $rate = $this->getExchangeRateFromDatabase($fromCurrency, $toCurrency);

        if ($rate !== null){
            return $rate;
        }
        // Fallback to external API
        $rate = $this->getExchangeRateFromAPI($fromCurrency, $toCurrency);
        if ($rate !== null){
            // Store in database for future use
            $this->storeExchangeRate($fromCurrency, $toCurrency);
            return $rate;
        }
        // Final fallback - return 1.0 and log error
        Log::error("Could not fetch exchange rate for {$fromCurrency} to {$toCurrency}");
        return 1.0;
    }

    // Get Exchange rate from database
    protected function getExchangeRateFromDatabase(string $fromCurrency, string $toCurrency):?float{

        //Direct rate
        $exchangeRate = ExchangeRate::where('from_currency', $fromCurrency)
        ->where('to_currency', $toCurrency)
        ->where('active', 1)
        ->orderBy('updated_at', 'desc')
        ->first();

        if ($exchangeRate){
            return $exchangeRate->rate;
        }

        // Try inverse rate
        $inverseRate = ExchangeRate::where('from_currency', $toCurrency)
        ->where('to_currency', $fromCurrency)
        ->where('active',1)
        ->orderBy('updated_at', 'desc')
        ->first();

        if ($inverseRate && $inverseRate->rate > 0) {
            return 1 /$inverseRate->rate;
        }
        return null;
    }

    // Get exchange rate  from API

    protected function getExchangeRateFromAPI(string $fromCurrency, string $toCurrency ):?float{
        try {
            $response = Http::timeout(10)->get("https://v6.exchangerate-api.com/v6/5321fa13d77eb52991aab21a/latest/{$fromCurrency}");

            if ($response->successful()){
                $data = $response->json();
                if(isset($data['rates'][$toCurrency])){
                    return (float) $data['rates'][$toCurrency];
                }
            }
        }
        catch(\Exception $e){
            Log::error("Failed to fetch exchange rate from API:" . $e->getMessage());
        }
        return null;
    }

    // Store exchange  rate in database
    protected function storeExchangeRate(string $fromCurrency, string $toCurrency, float $rate){
        try{
            ExchangeRate::updateOrCreate(
                [
                  'from_currency' => $fromCurrency,
                  'to_currency' => $toCurrency,
                ],
                [
                    'rate'=>$rate,
                    'active' => 1,
                    'updated_at'=> now(),
                ]
            );
        }
        catch(\Exception $e){
            Log::error("Failed to store exchange rate:" . $e->getMessage());
        }
    }

    // Get all supported currencies
    public function getSupportedCurrencies():array {
        return Currency::where('active', 1)
        ->orderBy('code')
        ->pluck('name', 'code')
        ->toArray();
    }

    // Update all exchange rates for a base currency
    public function updateExchangeRates(string $baseCurrency = null):bool {
        $baseCurrency = $baseCurrency  ?? $this->baseCurrency;

        try {
            $response = Http::timeout(30)->get("https://v6.exchangerate-api.com/v6/5321fa13d77eb52991aab21a/latest//{$baseCurrency}");
            if($response->successful()){
                $data = $response-> json();
                $rates = $data['rates'] ?? [];
            }

            foreach ($rates as $currency => $rate){
                if (Currency::where('code', $currency)->where('active', 1)->exist()){
                    $this->storeExchangeRate($baseCurrency, $currency, $rate);
                }
            }
            return true;
        }
        catch(\Exception $e){
            Log::error("Failed to update exchange rates:" . $e->getMessage());
        }
        return false;
    }

    // Clear exchange rate cache
    public function clearCache(){
        $currencies = Currency::where('active', 1)->pluck('code');

        foreach ($currencies as $from){
            foreach ($currencies as $to){
                if($from == $to){
                    Cache::forget("exchange_rate_{$from}_{$to}");
                }
            }
        }
    }
}
