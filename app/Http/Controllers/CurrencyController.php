<?php

namespace App\Http\Controllers;

use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly CurrencyExchangeService $currencyExchangeService
    )
    {
    }

    public function index(): Response
    {
        $symbols = $this->currencyExchangeService->getSymbols();

        return Inertia::render('currency/Index', [
            'currencies' => $symbols,
        ]);
    }
}
