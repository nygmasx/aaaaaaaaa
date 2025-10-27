export type CurrencyCode = string;

export type CountryCode = string;

export const currencyToCountry: Record<CurrencyCode, CountryCode> = {
    'USD': 'US',
    'EUR': 'EU',
    'GBP': 'GB',
    'JPY': 'JP',
    'CHF': 'CH',
    'CAD': 'CA',
    'AUD': 'AU',
    'CNY': 'CN',
    'INR': 'IN',
    'RUB': 'RU',
    'BRL': 'BR',
    'ZAR': 'ZA',
    'MXN': 'MX',
    'SGD': 'SG',
    'HKD': 'HK',
    'NOK': 'NO',
    'SEK': 'SE',
    'DKK': 'DK',
    'PLN': 'PL',
    'THB': 'TH',
    'MYR': 'MY',
    'IDR': 'ID',
    'PHP': 'PH',
    'CZK': 'CZ',
    'HUF': 'HU',
    'RON': 'RO',
    'NZD': 'NZ',
    'TRY': 'TR',
    'ILS': 'IL',
    'CLP': 'CL',
    'ARS': 'AR',
    'COP': 'CO',
    'PEN': 'PE',
    'AED': 'AE',
    'SAR': 'SA',
    'KRW': 'KR',
    'TWD': 'TW',
    'EGP': 'EG',
    'MAD': 'MA',
    'NGN': 'NG',
    'KES': 'KE',
    'PKR': 'PK',
    'BDT': 'BD',
    'VND': 'VN',
    'UAH': 'UA',
    'ISK': 'IS',
};

export function getCountryCode(currencyCode: CurrencyCode): CountryCode | null {
    return currencyToCountry[currencyCode] || null;
}

export function getCurrencyFlag(currencyCode: CurrencyCode): string {
    const countryCode = getCountryCode(currencyCode);
    if (!countryCode) return '🏳️';

    const codePoints = countryCode
        .toUpperCase()
        .split('')
        .map(char => 127397 + char.charCodeAt(0));

    return String.fromCodePoint(...codePoints);
}

export function hasCurrencyFlag(currencyCode: CurrencyCode): boolean {
    return currencyCode in currencyToCountry;
}

export function getAvailableCurrencies(): CurrencyCode[] {
    return Object.keys(currencyToCountry);
}
