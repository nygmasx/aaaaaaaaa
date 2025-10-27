<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Exchange;
use App\Models\User;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ExchangeController extends Controller
{
    public function __construct(
        private readonly CurrencyExchangeService $currencyExchangeService
    )
    {
    }

    public function index()
    {
        $currencies = Currency::all()->map(function ($currency) {
            return [
                'id' => $currency->id,
                'symbol' => $currency->symbol,
                'name' => $currency->name,
                'country_code' => $currency->country_code,
            ];
        });

        return Inertia::render('Transfer', [
            'currencies' => $currencies
        ]);
    }

    public function convert(Request $request)
    {
        $request->validate([
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $convertedAmount = $this->currencyExchangeService->convert(
                $request->amount,
                $request->from,
                $request->to
            );

            return response()->json([
                'success' => true,
                'converted_amount' => $convertedAmount,
                'exchange_rate' => $request->amount > 0 ? $convertedAmount / $request->amount : 1
            ]);
        } catch (\Exception $e) {
            Log::error('Currency conversion error: ' . $e->getMessage());

            $staticRates = [
                'EUR' => ['USD' => 1.09, 'GBP' => 0.86, 'JPY' => 161.2, 'CHF' => 1.02, 'CAD' => 1.47, 'AUD' => 1.63],
                'USD' => ['EUR' => 0.92, 'GBP' => 0.79, 'JPY' => 148.5, 'CHF' => 0.94, 'CAD' => 1.35, 'AUD' => 1.50],
                'GBP' => ['EUR' => 1.16, 'USD' => 1.27, 'JPY' => 188.3, 'CHF' => 1.19, 'CAD' => 1.71, 'AUD' => 1.90],
                'JPY' => ['EUR' => 0.0062, 'USD' => 0.0067, 'GBP' => 0.0053, 'CHF' => 0.0058, 'CAD' => 0.0084, 'AUD' => 0.0095],
            ];

            $fromCurrency = $request->from;
            $toCurrency = $request->to;
            $amount = $request->amount;

            if ($fromCurrency === $toCurrency) {
                $rate = 1;
                $convertedAmount = $amount;
            } else {
                $rate = $staticRates[$fromCurrency][$toCurrency] ?? 1;
                $convertedAmount = round($amount * $rate, 2);
            }

            return response()->json([
                'success' => true,
                'converted_amount' => $convertedAmount,
                'exchange_rate' => $rate,
                'fallback_mode' => true,
                'message' => 'Utilisation des taux de change de secours'
            ]);
        }
    }

    public function findUserByIban(Request $request)
    {
        $request->validate([
            'iban' => 'required|string|min:15'
        ]);

        $user = \App\Models\User::where('iban', $request->iban)->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'iban' => $user->iban
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucun utilisateur trouvé avec cet IBAN'
        ], 404);
    }

    public function store(Request $request)
    {
        Log::info('Store method called', ['request_data' => $request->all()]);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'recipient_iban' => 'required|string|min:15',
            'message' => 'nullable|string|max:500'
        ]);

        Log::info('Validation passed');

        try {
            DB::beginTransaction();

            $recipient = User::where('iban', $request->recipient_iban)->first();

            if (!$recipient) {
                return redirect()->back()->withErrors([
                    'recipient_iban' => 'Aucun utilisateur trouvé avec cet IBAN'
                ]);
            }

            if ($recipient->id === Auth::id()) {
                return redirect()->back()->withErrors([
                    'recipient_iban' => 'Vous ne pouvez pas vous transférer de l\'argent à vous-même'
                ]);
            }

            $sender = Auth::user();

            $exchangeRate = 1;
            if ($request->currency !== 'EUR') {
                try {
                    $convertedAmount = $this->currencyExchangeService->convert(
                        $request->amount,
                        $request->currency,
                        'EUR'
                    );
                    $exchangeRate = $convertedAmount / $request->amount;
                } catch (\Exception $e) {
                    $staticRates = [
                        'USD' => 0.92,
                        'GBP' => 1.16,
                        'JPY' => 0.0062,
                        'CHF' => 0.98,
                        'CAD' => 0.68,
                        'AUD' => 0.61
                    ];
                    $exchangeRate = $staticRates[$request->currency] ?? 1;
                }
            }

            $amountInEur = $request->amount * $exchangeRate;

            if ($sender->balance < $amountInEur) {
                return redirect()->back()->withErrors([
                    'amount' => 'Solde insuffisant. Votre solde actuel est de ' . number_format($sender->balance, 2) . ' EUR'
                ]);
            }

            $exchange = Exchange::create([
                'sender_id' => $sender->id,
                'receiver_id' => $recipient->id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'exchange_rate' => $exchangeRate,
                'message' => $request->message
            ]);

            $sender->balance -= $amountInEur;
            $sender->save();

            $recipient->balance += $amountInEur;
            $recipient->save();

            DB::commit();

            $redirectRoute = $sender->role === 'ROLE_ADMIN' ? '/admin/exchanges' : route('dashboard');

            return redirect($redirectRoute)->with('success',
                'Transfert de ' . number_format($request->amount, 2) . ' ' . $request->currency .
                ' vers ' . $recipient->name . ' effectué avec succès !'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer error: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'general' => 'Une erreur est survenue lors du transfert. Veuillez réessayer.'
            ]);
        }
    }
}
