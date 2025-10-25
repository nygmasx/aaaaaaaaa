<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'balance',
        'iban'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->iban)) {
                $user->iban = self::generateFrenchIban();
            }
        });
    }

    private static function generateFrenchIban(): string
    {
        $countryCode = 'FR';

        $bankCode = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);

        $branchCode = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);

        $accountNumber = '';
        for ($i = 0; $i < 11; $i++) {
            $accountNumber .= rand(0, 9);
        }

        $ribKey = self::calculateRibKey($bankCode, $branchCode, $accountNumber);

        $bban = $bankCode . $branchCode . $accountNumber . $ribKey;

        $ibanKey = self::calculateIbanKey($countryCode, $bban);

        return $countryCode . $ibanKey . $bban;
    }

    private static function calculateRibKey(string $bankCode, string $branchCode, string $accountNumber): string
    {
        $total = (int)$bankCode * 89 + (int)$branchCode * 15 + (int)$accountNumber * 3;
        $remainder = $total % 97;
        $key = 97 - $remainder;

        return str_pad($key, 2, '0', STR_PAD_LEFT);
    }

    private static function calculateIbanKey(string $countryCode, string $bban): string
    {
        $numericCountryCode = '';
        for ($i = 0; $i < strlen($countryCode); $i++) {
            $numericCountryCode .= ord($countryCode[$i]) - ord('A') + 10;
        }

        $checkString = $bban . $numericCountryCode . '00';

        $remainder = 0;
        for ($i = 0; $i < strlen($checkString); $i++) {
            $remainder = ($remainder * 10 + (int)$checkString[$i]) % 97;
        }

        $checkDigits = 98 - $remainder;

        return str_pad($checkDigits, 2, '0', STR_PAD_LEFT);
    }
}
