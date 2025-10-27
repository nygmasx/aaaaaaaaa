<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = \App\Models\User::all();
        $adminUsers = $users->where('role', 'ROLE_ADMIN');
        $regularUsers = $users->where('role', 'ROLE_USER');
        $activeUsers = $users->where('is_active', true);
        $inactiveUsers = $users->where('is_active', false);

        foreach ($regularUsers->take(20) as $sender) {
            if (!$sender->is_active) continue;

            $receivers = $regularUsers->where('id', '!=', $sender->id)->where('is_active', true)->take(3);
            foreach ($receivers as $receiver) {
                \App\Models\Exchange::factory()->create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                ]);
            }
        }

        \App\Models\Exchange::factory(10)->smallAmount()->create([
            'sender_id' => $regularUsers->random()->id,
            'receiver_id' => $regularUsers->random()->id,
        ]);

        \App\Models\Exchange::factory(5)->largeAmount()->create([
            'sender_id' => $regularUsers->where('balance', '>', 10000)->random()->id,
            'receiver_id' => $regularUsers->random()->id,
        ]);

        \App\Models\Exchange::factory(15)->recent()->create([
            'sender_id' => $activeUsers->random()->id,
            'receiver_id' => $activeUsers->random()->id,
        ]);

        \App\Models\Exchange::factory(10)->old()->create([
            'sender_id' => $regularUsers->random()->id,
            'receiver_id' => $regularUsers->random()->id,
        ]);

        \App\Models\Exchange::factory(20)->withMessage()->create([
            'sender_id' => $activeUsers->random()->id,
            'receiver_id' => $activeUsers->random()->id,
        ]);

        \App\Models\Exchange::factory(10)->withoutMessage()->create([
            'sender_id' => $activeUsers->random()->id,
            'receiver_id' => $activeUsers->random()->id,
        ]);

        \App\Models\Exchange::factory(25)->eur()->create([
            'sender_id' => $activeUsers->random()->id,
            'receiver_id' => $activeUsers->random()->id,
        ]);

        \App\Models\Exchange::factory(15)->usd()->create([
            'sender_id' => $activeUsers->random()->id,
            'receiver_id' => $activeUsers->random()->id,
        ]);

        $testUser = $users->where('email', 'pierre.user@example.com')->first();
        if ($testUser) {
            \App\Models\Exchange::factory(5)->create([
                'sender_id' => $testUser->id,
                'receiver_id' => $regularUsers->where('id', '!=', $testUser->id)->random()->id,
            ]);

            \App\Models\Exchange::factory(3)->create([
                'sender_id' => $regularUsers->where('id', '!=', $testUser->id)->random()->id,
                'receiver_id' => $testUser->id,
            ]);
        }

        $richUser = $users->where('email', 'marie.riche@example.com')->first();
        if ($richUser) {
            \App\Models\Exchange::factory(8)->largeAmount()->create([
                'sender_id' => $richUser->id,
                'receiver_id' => $regularUsers->where('id', '!=', $richUser->id)->random()->id,
            ]);
        }

        $poorUser = $users->where('email', 'paul.pauvre@example.com')->first();
        if ($poorUser) {
            \App\Models\Exchange::factory(3)->smallAmount()->create([
                'sender_id' => $poorUser->id,
                'receiver_id' => $regularUsers->where('id', '!=', $poorUser->id)->random()->id,
            ]);
        }


        $currencies = ['GBP', 'JPY', 'CAD', 'CHF'];
        foreach ($currencies as $currency) {
            \App\Models\Exchange::factory(3)->create([
                'sender_id' => $activeUsers->random()->id,
                'receiver_id' => $activeUsers->random()->id,
                'currency' => $currency,
            ]);
        }

        \App\Models\Exchange::factory(30)->create([
            'sender_id' => $activeUsers->random()->id,
            'receiver_id' => $activeUsers->random()->id,
        ]);

        $this->command->info('=== RÉSUMÉ DES ÉCHANGES CRÉÉS ===');
        $this->command->info('Total échanges: ' . \App\Models\Exchange::count());
        $this->command->info('Échanges en EUR: ' . \App\Models\Exchange::where('currency', 'EUR')->count());
        $this->command->info('Échanges en USD: ' . \App\Models\Exchange::where('currency', 'USD')->count());
        $this->command->info('Échanges avec message: ' . \App\Models\Exchange::whereNotNull('message')->count());
        $this->command->info('Échanges récents (7 derniers jours): ' . \App\Models\Exchange::where('created_at', '>=', now()->subWeek())->count());
        $this->command->info('Montant total échangé: ' . number_format(\App\Models\Exchange::sum('amount'), 2) . ' (toutes devises)');
    }
}
