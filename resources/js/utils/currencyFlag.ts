/**
 * Type pour les codes de devises ISO 4217
 */
export type CurrencyCode = string;

/**
 * Type pour les codes pays ISO 3166-1 alpha-2
 */
export type CountryCode = string;

/**
 * Mapping devise ISO -> code pays ISO pour les drapeaux
 */
export const currencyToCountry: Record<CurrencyCode, CountryCode> = {
    'USD': 'US', // Dollar américain
    'EUR': 'EU', // Euro
    'GBP': 'GB', // Livre sterling
    'JPY': 'JP', // Yen japonais
    'CHF': 'CH', // Franc suisse
    'CAD': 'CA', // Dollar canadien
    'AUD': 'AU', // Dollar australien
    'CNY': 'CN', // Yuan chinois
    'INR': 'IN', // Roupie indienne
    'RUB': 'RU', // Rouble russe
    'BRL': 'BR', // Real brésilien
    'ZAR': 'ZA', // Rand sud-africain
    'MXN': 'MX', // Peso mexicain
    'SGD': 'SG', // Dollar de Singapour
    'HKD': 'HK', // Dollar de Hong Kong
    'NOK': 'NO', // Couronne norvégienne
    'SEK': 'SE', // Couronne suédoise
    'DKK': 'DK', // Couronne danoise
    'PLN': 'PL', // Zloty polonais
    'THB': 'TH', // Baht thaïlandais
    'MYR': 'MY', // Ringgit malaisien
    'IDR': 'ID', // Roupie indonésienne
    'PHP': 'PH', // Peso philippin
    'CZK': 'CZ', // Couronne tchèque
    'HUF': 'HU', // Forint hongrois
    'RON': 'RO', // Leu roumain
    'NZD': 'NZ', // Dollar néo-zélandais
    'TRY': 'TR', // Livre turque
    'ILS': 'IL', // Shekel israélien
    'CLP': 'CL', // Peso chilien
    'ARS': 'AR', // Peso argentin
    'COP': 'CO', // Peso colombien
    'PEN': 'PE', // Sol péruvien
    'AED': 'AE', // Dirham des EAU
    'SAR': 'SA', // Riyal saoudien
    'KRW': 'KR', // Won sud-coréen
    'TWD': 'TW', // Dollar de Taïwan
    'EGP': 'EG', // Livre égyptienne
    'MAD': 'MA', // Dirham marocain
    'NGN': 'NG', // Naira nigérian
    'KES': 'KE', // Shilling kenyan
    'PKR': 'PK', // Roupie pakistanaise
    'BDT': 'BD', // Taka bangladais
    'VND': 'VN', // Dong vietnamien
    'UAH': 'UA', // Hryvnia ukrainienne
    'ISK': 'IS', // Couronne islandaise
    // Ajoutez d'autres devises selon vos besoins
};

/**
 * Obtenir le code pays pour une devise
 * @param currencyCode - Code ISO 4217 de la devise (ex: "USD", "EUR")
 * @returns Code pays ISO 3166-1 alpha-2 ou null si non trouvé
 */
export function getCountryCode(currencyCode: CurrencyCode): CountryCode | null {
    return currencyToCountry[currencyCode] || null;
}

/**
 * Obtenir l'emoji du drapeau pour une devise
 * @param currencyCode - Code ISO 4217 de la devise (ex: "USD", "EUR")
 * @returns Emoji du drapeau correspondant ou drapeau blanc si non trouvé
 */
export function getCurrencyFlag(currencyCode: CurrencyCode): string {
    const countryCode = getCountryCode(currencyCode);
    if (!countryCode) return '🏳️';

    // Convertir le code pays en emoji drapeau
    // Les emojis de drapeaux sont composés de deux caractères Unicode Regional Indicator
    const codePoints = countryCode
        .toUpperCase()
        .split('')
        .map(char => 127397 + char.charCodeAt(0));

    return String.fromCodePoint(...codePoints);
}

/**
 * Vérifier si une devise a un drapeau disponible
 * @param currencyCode - Code ISO 4217 de la devise
 * @returns true si un drapeau est disponible
 */
export function hasCurrencyFlag(currencyCode: CurrencyCode): boolean {
    return currencyCode in currencyToCountry;
}

/**
 * Obtenir toutes les devises avec drapeaux disponibles
 * @returns Liste des codes de devises avec drapeaux
 */
export function getAvailableCurrencies(): CurrencyCode[] {
    return Object.keys(currencyToCountry);
}
