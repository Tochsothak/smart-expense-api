<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRateModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyConversionService
{
    protected string $baseCurrency;
    protected int $cacheMinutes;

    public function __construct()
    {
        $this->baseCurrency = config('app.base_currency', 'USD');
        $this->cacheMinutes = config('app.exchange_rate_cache_minutes', 60);
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        // Same currency, no conversion needed
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $exchangeRate = $this->getExchangeRate($fromCurrency, $toCurrency);

        return round($amount * $exchangeRate, 2);
    }

    /**
     * Get exchange rate between two currencies
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        // Same currency
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}";

        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($fromCurrency, $toCurrency) {
            return $this->fetchExchangeRate($fromCurrency, $toCurrency);
        });
    }

    /**
     * Fetch exchange rate from database or external API
     */
    protected function fetchExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        // Try to get from database first
        $rate = $this->getExchangeRateFromDatabase($fromCurrency, $toCurrency);

        if ($rate !== null) {
            return $rate;
        }

        // Fallback to external API
        $rate = $this->getExchangeRateFromAPI($fromCurrency, $toCurrency);

        if ($rate !== null) {
            // Store in database for future use
            $this->storeExchangeRate($fromCurrency, $toCurrency, $rate);
            return $rate;
        }

        // Final fallback - return 1.0 and log error
        Log::error("Could not fetch exchange rate for {$fromCurrency} to {$toCurrency}");
        return 1.0;
    }

    /**
     * Get exchange rate from database
     */
    protected function getExchangeRateFromDatabase(string $fromCurrency, string $toCurrency): ?float
    {
        // Direct rate
        $exchangeRate = ExchangeRateModel::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('active', true)
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($exchangeRate) {
            return $exchangeRate->rate;
        }

        // Try inverse rate
        $inverseRate = ExchangeRateModel::where('from_currency', $toCurrency)
            ->where('to_currency', $fromCurrency)
            ->where('active', true)
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($inverseRate && $inverseRate->rate > 0) {
            return 1 / $inverseRate->rate;
        }

        return null;
    }

    /**
     * Get exchange rate from external API
     */
    protected function getExchangeRateFromAPI(string $fromCurrency, string $toCurrency): ?float
    {
        try {
            // Example using a free API (you can replace with your preferred service)
            $response = Http::timeout(10)->get("https://v6.exchangerate-api.com/v6/5321fa13d77eb52991aab21a/latest/{$fromCurrency}");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['conversion_rates'][$toCurrency])) {
                    return (float) $data['conversion_rates'][$toCurrency];
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch exchange rate from API: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Store exchange rate in database
     */
    public function storeExchangeRate(string $fromCurrency, string $toCurrency, float $rate): void
    {
        try {
            ExchangeRateModel::updateOrCreate(
                [
                    'from_currency' => $fromCurrency,
                    'to_currency' => $toCurrency,
                ],
                [
                    'rate' => $rate,
                    'active' => 1,
                    'updated_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            Log::error("Failed to store exchange rate: " . $e->getMessage());
        }
    }

    /**
     * Get all supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return Currency::where('active', true)
            ->orderBy('code')
            ->pluck('name', 'code')
            ->toArray();
    }

    /**
     * Update all exchange rates for a base currency
     */
    public function updateExchangeRates(string $baseCurrency = null): bool
    {
        $baseCurrency = $baseCurrency ?? $this->baseCurrency;

        try {
            $response = Http::timeout(30)->get("https://v6.exchangerate-api.com/v6/5321fa13d77eb52991aab21a/latest/{$baseCurrency}");

            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['conversion_rates'] ?? [];

                foreach ($rates as $currency => $rate) {
                    if (Currency::where('code', $currency)->where('active', 1)->exists()) {
                        $this->storeExchangeRate($baseCurrency, $currency, $rate);
                    }
                }

                return true;
            }
        } catch (\Exception $e) {
            Log::error("Failed to update exchange rates: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Clear exchange rate cache
     */
    public function clearCache(): void
    {
        $currencies = Currency::where('active', 1)->pluck('code');

        foreach ($currencies as $from) {
            foreach ($currencies as $to) {
                if ($from !== $to) {
                    Cache::forget("exchange_rate_{$from}_{$to}");
                }
            }
        }
    }
}
