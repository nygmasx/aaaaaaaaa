<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CurrencyExchangeService
{
    private string $apiKey;
    private string $baseUrl = 'http://data.fixer.io/api';

    public function __construct()
    {
        $this->apiKey = env('FIXER_API_KEY');
    }

    public function getRates(string $base = 'EUR', array $symbols = []): array
    {
        $cacheKey = "fixer_rates_{$base}_" . implode('_', $symbols);

        return Cache::remember($cacheKey, 3600, function () use ($base, $symbols) {
            $params = [
                'access_key' => $this->apiKey,
                'base' => $base,
            ];

            if (!empty($symbols)) {
                $params['symbols'] = implode(',', $symbols);
            }

            $response = Http::get("{$this->baseUrl}/latest", $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Erreur API Fixer: ' . $response->body());
        });
    }

    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rates = $this->getRates('EUR', [$from, $to]);

        if (!isset($rates['rates'])) {
            throw new \Exception("Erreur lors de la récupération des taux de change");
        }

        $fromRate = $from === 'EUR' ? 1 : ($rates['rates'][$from] ?? null);
        $toRate = $to === 'EUR' ? 1 : ($rates['rates'][$to] ?? null);

        if ($fromRate === null) {
            throw new \Exception("Taux non disponible pour la devise {$from}");
        }

        if ($toRate === null) {
            throw new \Exception("Taux non disponible pour la devise {$to}");
        }

        $eurAmount = $amount / $fromRate;
        $convertedAmount = $eurAmount * $toRate;

        return round($convertedAmount, 2);
    }

    public function getSymbols(): array
    {
        return Cache::remember('fixer_symbols', 86400, function () {
            $response = Http::get("{$this->baseUrl}/symbols", [
                'access_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json()['symbols'] ?? [];
            }

            return [];
        });
    }
}
