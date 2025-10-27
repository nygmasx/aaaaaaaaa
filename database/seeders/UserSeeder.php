<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'balance' => 50000.00,
        ]);

        User::factory()->admin()->create([
            'name' => 'Jean Administrateur',
            'email' => 'jean.admin@example.com',
            'password' => 'password',
            'balance' => 25000.00,
        ]);

        User::factory()->highBalance()->create([
            'name' => 'Marie Riche',
            'email' => 'marie.riche@example.com',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Pierre Utilisateur',
            'email' => 'pierre.user@example.com',
            'password' => 'password',
            'balance' => 1500.50,
        ]);

        User::factory()->lowBalance()->create([
            'name' => 'Paul Pauvre',
            'email' => 'paul.pauvre@example.com',
            'password' => 'password',
        ]);

        User::factory()->withTwoFactor()->create([
            'name' => 'Sophie Sécurisée',
            'email' => 'sophie.secure@example.com',
            'password' => 'password',
            'balance' => 3000.00,
        ]);

        User::factory()->unverified()->create([
            'name' => 'Lucas Non-Vérifié',
            'email' => 'lucas.unverified@example.com',
            'password' => 'password',
            'balance' => 500.00,
        ]);

        User::factory()->inactive()->create([
            'name' => 'Martin Bloqué',
            'email' => 'martin.blocked@example.com',
            'password' => 'password',
            'balance' => 1000.00,
        ]);

        User::factory()->admin()->withTwoFactor()->create([
            'name' => 'Claire Admin Sécurisée',
            'email' => 'claire.admin.secure@example.com',
            'password' => 'password',
            'balance' => 30000.00,
        ]);

        User::factory()->unverified()->lowBalance()->create([
            'name' => 'Thomas Nouveau',
            'email' => 'thomas.nouveau@example.com',
            'password' => 'password',
        ]);

        User::factory(20)->create();

        User::factory(5)->highBalance()->create();

        User::factory(5)->lowBalance()->create();

        User::factory(5)->withTwoFactor()->create();

        User::factory(5)->unverified()->create();

        $this->command->info('=== RÉSUMÉ DES UTILISATEURS CRÉÉS ===');
        $this->command->info('Admins: ' . User::where('role', 'ROLE_ADMIN')->count());
        $this->command->info('Utilisateurs normaux: ' . User::where('role', 'ROLE_USER')->count());
        $this->command->info('Utilisateurs inactifs: ' . User::where('is_active', false)->count());
        $this->command->info('Utilisateurs avec 2FA: ' . User::whereNotNull('two_factor_confirmed_at')->count());
        $this->command->info('Utilisateurs non vérifiés: ' . User::whereNull('email_verified_at')->count());
        $this->command->info('Total utilisateurs: ' . User::count());
    }
}