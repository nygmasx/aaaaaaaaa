<script setup lang="ts">
import { getCurrencyFlag, hasCurrencyFlag } from '@/utils/currencyFlag'
import AppLayout from '@/layouts/AppLayout.vue';

const { currencies } = defineProps({
    currencies: Object
})

console.log(currencies);

const getFlagForCurrency = (currencyCode: string): string => {
    return getCurrencyFlag(currencyCode)
}

const hasFlag = (currencyCode: string): boolean => {
    return hasCurrencyFlag(currencyCode)
}

</script>

<template>
    <AppLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Liste des Devises</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="(currency, code) in currencies"
                    :key="code"
                    class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow"
                >
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ getFlagForCurrency(code) }}</span>
                        <div>
                            <h3 class="font-semibold text-lg">{{ code }}</h3>
                            <p class="text-gray-600 text-sm">{{ currency.name || 'Nom non disponible' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-sm text-gray-500">
                            Taux: {{ currency.rate || 'N/A' }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs px-2 py-1 rounded-full"
                              :class="hasFlag(code) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                        >
                            {{ hasFlag(code) ? 'Drapeau disponible' : 'Drapeau par défaut' }}
                        </span>
                        </div>
                    </div>
                </div>
            </div>

            <details class="mt-8">
                <summary class="cursor-pointer text-gray-600 hover:text-gray-800">Données brutes (debug)</summary>
                <pre class="mt-2 bg-gray-100 p-4 rounded text-xs overflow-auto">{{ currencies }}</pre>
            </details>
        </div>
    </AppLayout>
</template>

<style scoped>

</style>
