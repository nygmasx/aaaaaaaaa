<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;

        $transfersData = Exchange::where('sender_id', $user->id)
            ->whereYear('created_at', $currentYear)
            ->select(
                DB::raw('CAST(strftime("%m", created_at) AS INTEGER) as month'),
                DB::raw('COUNT(*) as transfers')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthsData = [];
        $monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

        for ($i = 1; $i <= 12; $i++) {
            $transferCount = $transfersData->where('month', $i)->first();
            $monthsData[] = [
                'name' => $monthNames[$i - 1],
                'transfers' => $transferCount ? $transferCount->transfers : 0
            ];
        }

        $recentTransfers = Exchange::where('sender_id', $user->id)
            ->with('receiver')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($exchange) {
                return [
                    'id' => $exchange->id,
                    'recipient' => $exchange->receiver->name,
                    'amount' => $exchange->amount,
                    'currency' => $exchange->currency,
                    'date' => $exchange->created_at->format('Y-m-d'),
                    'status' => 'Terminé'
                ];
            });

        $totalTransfers = Exchange::where('sender_id', $user->id)
            ->whereYear('created_at', $currentYear)
            ->count();

        return Inertia::render('Dashboard', [
            'transfersData' => $monthsData,
            'recentTransfers' => $recentTransfers,
            'totalTransfers' => $totalTransfers
        ]);
    }
}
