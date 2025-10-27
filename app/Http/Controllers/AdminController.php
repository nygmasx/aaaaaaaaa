<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{

    public function users(Request $request)
    {
        $search = $request->get('search', '');

        $users = User::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('iban', 'like', "%{$search}%");
                });
            })
            ->withCount(['sentExchanges', 'receivedExchanges'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => [
                'search' => $search
            ]
        ]);
    }

    public function userDetails($id)
    {
        $user = User::with(['sentExchanges.receiver', 'receivedExchanges.sender'])
            ->withCount(['sentExchanges', 'receivedExchanges'])
            ->findOrFail($id);

        $allExchanges = collect()
            ->merge($user->sentExchanges->map(function ($exchange) {
                return [
                    'id' => $exchange->id,
                    'type' => 'sent',
                    'other_user' => $exchange->receiver->name,
                    'other_user_email' => $exchange->receiver->email,
                    'amount' => $exchange->amount,
                    'currency' => $exchange->currency,
                    'exchange_rate' => $exchange->exchange_rate,
                    'message' => $exchange->message,
                    'created_at' => $exchange->created_at,
                    'date' => $exchange->created_at->format('Y-m-d H:i')
                ];
            }))
            ->merge($user->receivedExchanges->map(function ($exchange) {
                return [
                    'id' => $exchange->id,
                    'type' => 'received',
                    'other_user' => $exchange->sender->name,
                    'other_user_email' => $exchange->sender->email,
                    'amount' => $exchange->amount,
                    'currency' => $exchange->currency,
                    'exchange_rate' => $exchange->exchange_rate,
                    'message' => $exchange->message,
                    'created_at' => $exchange->created_at,
                    'date' => $exchange->created_at->format('Y-m-d H:i')
                ];
            }))
            ->sortByDesc('created_at')
            ->values();

        return Inertia::render('Admin/UserDetails', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'iban' => $user->iban,
                'balance' => $user->balance,
                'role' => $user->role,
                'blocked' => !($user->is_active ?? true),
                'created_at' => $user->created_at->format('Y-m-d H:i'),
                'sent_exchanges_count' => $user->sent_exchanges_count,
                'received_exchanges_count' => $user->received_exchanges_count
            ],
            'exchanges' => $allExchanges
        ]);
    }

    public function toggleAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas modifier vos propres droits']);
        }

        $user->role = $user->role === 'ROLE_ADMIN' ? 'ROLE_USER' : 'ROLE_ADMIN';
        $user->save();

        return back()->with('success',
            $user->role === 'ROLE_ADMIN'
                ? 'Utilisateur promu administrateur'
                : 'Droits administrateur retirés'
        );
    }

    public function toggleBlock(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas vous bloquer vous-même']);
        }

        $user->is_active = !($user->is_active ?? true);
        $user->save();

        return back()->with('success',
            !$user->is_active
                ? 'Utilisateur bloqué'
                : 'Utilisateur débloqué'
        );
    }

    public function exchanges(Request $request)
    {
        $search = $request->get('search', '');

        $exchanges = Exchange::with(['sender', 'receiver'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereHas('sender', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('receiver', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('currency', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalExchanges = Exchange::count();
        $totalAmount = Exchange::sum(\Illuminate\Support\Facades\DB::raw('amount * exchange_rate'));
        $todayExchanges = Exchange::whereDate('created_at', today())->count();

        return Inertia::render('Admin/Exchanges', [
            'exchanges' => $exchanges->through(function ($exchange) {
                return [
                    'id' => $exchange->id,
                    'sender' => [
                        'id' => $exchange->sender->id,
                        'name' => $exchange->sender->name,
                        'email' => $exchange->sender->email,
                    ],
                    'receiver' => [
                        'id' => $exchange->receiver->id,
                        'name' => $exchange->receiver->name,
                        'email' => $exchange->receiver->email,
                    ],
                    'amount' => $exchange->amount,
                    'currency' => $exchange->currency,
                    'exchange_rate' => $exchange->exchange_rate,
                    'amount_eur' => $exchange->amount * $exchange->exchange_rate,
                    'message' => $exchange->message,
                    'created_at' => $exchange->created_at->format('Y-m-d H:i'),
                    'date' => $exchange->created_at->format('d/m/Y'),
                    'time' => $exchange->created_at->format('H:i'),
                ];
            }),
            'filters' => [
                'search' => $search
            ],
            'stats' => [
                'total_exchanges' => $totalExchanges,
                'total_amount' => $totalAmount,
                'today_exchanges' => $todayExchanges,
            ]
        ]);
    }
}
