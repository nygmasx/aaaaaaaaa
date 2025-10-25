<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['name' => 'Dollar américain', 'symbol' => 'USD', 'country_code' => 'US'],
            ['name' => 'Euro', 'symbol' => 'EUR', 'country_code' => 'EU'],
            ['name' => 'Livre sterling', 'symbol' => 'GBP', 'country_code' => 'GB'],
            ['name' => 'Yen japonais', 'symbol' => 'JPY', 'country_code' => 'JP'],
            ['name' => 'Franc suisse', 'symbol' => 'CHF', 'country_code' => 'CH'],
            ['name' => 'Dollar canadien', 'symbol' => 'CAD', 'country_code' => 'CA'],
            ['name' => 'Dollar australien', 'symbol' => 'AUD', 'country_code' => 'AU'],
            ['name' => 'Yuan chinois', 'symbol' => 'CNY', 'country_code' => 'CN'],
            ['name' => 'Roupie indienne', 'symbol' => 'INR', 'country_code' => 'IN'],
            ['name' => 'Rouble russe', 'symbol' => 'RUB', 'country_code' => 'RU'],
            ['name' => 'Real brésilien', 'symbol' => 'BRL', 'country_code' => 'BR'],
            ['name' => 'Rand sud-africain', 'symbol' => 'ZAR', 'country_code' => 'ZA'],
            ['name' => 'Peso mexicain', 'symbol' => 'MXN', 'country_code' => 'MX'],
            ['name' => 'Dollar de Singapour', 'symbol' => 'SGD', 'country_code' => 'SG'],
            ['name' => 'Dollar de Hong Kong', 'symbol' => 'HKD', 'country_code' => 'HK'],
            ['name' => 'Couronne norvégienne', 'symbol' => 'NOK', 'country_code' => 'NO'],
            ['name' => 'Couronne suédoise', 'symbol' => 'SEK', 'country_code' => 'SE'],
            ['name' => 'Couronne danoise', 'symbol' => 'DKK', 'country_code' => 'DK'],
            ['name' => 'Zloty polonais', 'symbol' => 'PLN', 'country_code' => 'PL'],
            ['name' => 'Baht thaïlandais', 'symbol' => 'THB', 'country_code' => 'TH'],
            ['name' => 'Ringgit malaisien', 'symbol' => 'MYR', 'country_code' => 'MY'],
            ['name' => 'Roupie indonésienne', 'symbol' => 'IDR', 'country_code' => 'ID'],
            ['name' => 'Peso philippin', 'symbol' => 'PHP', 'country_code' => 'PH'],
            ['name' => 'Couronne tchèque', 'symbol' => 'CZK', 'country_code' => 'CZ'],
            ['name' => 'Forint hongrois', 'symbol' => 'HUF', 'country_code' => 'HU'],
            ['name' => 'Leu roumain', 'symbol' => 'RON', 'country_code' => 'RO'],
            ['name' => 'Dollar néo-zélandais', 'symbol' => 'NZD', 'country_code' => 'NZ'],
            ['name' => 'Livre turque', 'symbol' => 'TRY', 'country_code' => 'TR'],
            ['name' => 'Shekel israélien', 'symbol' => 'ILS', 'country_code' => 'IL'],
            ['name' => 'Peso chilien', 'symbol' => 'CLP', 'country_code' => 'CL'],
            ['name' => 'Peso argentin', 'symbol' => 'ARS', 'country_code' => 'AR'],
            ['name' => 'Peso colombien', 'symbol' => 'COP', 'country_code' => 'CO'],
            ['name' => 'Sol péruvien', 'symbol' => 'PEN', 'country_code' => 'PE'],
            ['name' => 'Dirham des EAU', 'symbol' => 'AED', 'country_code' => 'AE'],
            ['name' => 'Riyal saoudien', 'symbol' => 'SAR', 'country_code' => 'SA'],
            ['name' => 'Won sud-coréen', 'symbol' => 'KRW', 'country_code' => 'KR'],
            ['name' => 'Dollar de Taïwan', 'symbol' => 'TWD', 'country_code' => 'TW'],
            ['name' => 'Livre égyptienne', 'symbol' => 'EGP', 'country_code' => 'EG'],
            ['name' => 'Dirham marocain', 'symbol' => 'MAD', 'country_code' => 'MA'],
            ['name' => 'Naira nigérian', 'symbol' => 'NGN', 'country_code' => 'NG'],
            ['name' => 'Shilling kenyan', 'symbol' => 'KES', 'country_code' => 'KE'],
            ['name' => 'Roupie pakistanaise', 'symbol' => 'PKR', 'country_code' => 'PK'],
            ['name' => 'Taka bangladais', 'symbol' => 'BDT', 'country_code' => 'BD'],
            ['name' => 'Dong vietnamien', 'symbol' => 'VND', 'country_code' => 'VN'],
            ['name' => 'Hryvnia ukrainienne', 'symbol' => 'UAH', 'country_code' => 'UA'],
            ['name' => 'Couronne islandaise', 'symbol' => 'ISK', 'country_code' => 'IS'],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['symbol' => $currency['symbol']],
                $currency
            );
        }
    }
}
