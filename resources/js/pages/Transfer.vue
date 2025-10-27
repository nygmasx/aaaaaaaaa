<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { transfer } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { getCurrencyFlag } from '@/utils/currencyFlag';
import { ref, watch, computed } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { ChevronRight, ChevronLeft, User, CreditCard, ArrowRightLeft } from 'lucide-vue-next';
import axios from 'axios';

const { currencies } = defineProps<{
    currencies: Array<{
        id: number;
        symbol: string;
        name: string;
        country_code: string;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard'
    },
    {
        title: 'Transfert d\'argent',
        href: transfer().url
    }
];

const currentStep = ref(1);
const totalSteps = 3;

const form = useForm({
    amount: 0,
    currency: 'EUR',
    recipient_iban: '',
    message: ''
});

const exchangeData = ref({
    rate: 0,
    convertedAmount: 0,
    loading: false
});

const recipientUser = ref(null);
const ibanSearchLoading = ref(false);

const steps = [
    { number: 1, title: 'Montant', description: 'Choisissez le montant et la devise', icon: CreditCard },
    { number: 2, title: 'Destinataire', description: 'Trouvez le destinataire par IBAN', icon: User },
    { number: 3, title: 'Confirmation', description: 'Vérifiez et envoyez', icon: ArrowRightLeft }
];

const canProceedFromStep1 = computed(() => form.amount > 0 && form.currency);
const canProceedFromStep2 = computed(() => recipientUser.value !== null);
const currentStepData = computed(() => steps[currentStep.value - 1]);

const nextStep = () => {
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const searchUserByIban = async () => {
    if (!form.recipient_iban || form.recipient_iban.length < 15) return;

    ibanSearchLoading.value = true;

    try {
        const response = await axios.post('/exchange/find-user', {
            iban: form.recipient_iban
        });

        if (response.data.success) {
            recipientUser.value = response.data.user;
        }
    } catch (error) {
        recipientUser.value = null;
        console.error('Utilisateur non trouvé');
    } finally {
        ibanSearchLoading.value = false;
    }
};

watch(() => form.recipient_iban, searchUserByIban, { debounce: 800 });

const getConversionToEur = async () => {
    if (!form.amount || form.amount <= 0 || !form.currency) {
        exchangeData.value.convertedAmount = 0;
        exchangeData.value.rate = 0;
        return;
    }

    if (form.currency === 'EUR') {
        exchangeData.value.rate = 1;
        exchangeData.value.convertedAmount = form.amount;
        return;
    }

    exchangeData.value.loading = true;

    try {
        const response = await axios.post('/exchange/convert', {
            from: form.currency,
            to: 'EUR',
            amount: form.amount
        });

        if (response.data.success) {
            exchangeData.value.rate = response.data.exchange_rate;
            exchangeData.value.convertedAmount = response.data.converted_amount;
        } else {
            console.error('Erreur de conversion:', response.data.message);
            exchangeData.value.rate = 1;
            exchangeData.value.convertedAmount = form.amount;
        }
    } catch (error) {
        console.error('Erreur lors de la conversion:', error);

        const fallbackRates = {
            'USD': 0.92,
            'GBP': 1.16,
            'JPY': 0.0062,
            'CHF': 0.98,
            'CAD': 0.68,
            'AUD': 0.61
        };

        const fallbackRate = fallbackRates[form.currency] || 1;
        exchangeData.value.rate = fallbackRate;
        exchangeData.value.convertedAmount = form.amount * fallbackRate;
    } finally {
        exchangeData.value.loading = false;
    }
};

watch([() => form.amount, () => form.currency], getConversionToEur, { debounce: 500 });

const submitTransfer = () => {
    console.log('Submitting transfer with data:', form.data());

    form.post('/exchange/store', {
        onSuccess: (response) => {
            console.log('Transfer successful:', response);
        },
        onError: (errors) => {
            console.error('Erreurs de transfert:', errors);
            alert('Erreur: ' + JSON.stringify(errors));
        },
        onBefore: () => {
            console.log('Starting transfer submission...');
        },
        onStart: () => {
            console.log('Transfer request started...');
        },
        onProgress: (progress) => {
            console.log('Progress:', progress);
        },
        onFinish: () => {
            console.log('Transfer request finished');
        }
    });
};
</script>

<template>
    <Head title="Transfert d'argent" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full max-w-4xl mx-auto p-6 space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">Nouveau transfert</h1>
                <p class="text-gray-600 mt-2">Transférez de l'argent en toute sécurité</p>
            </div>

            <div class="w-full">
                <div class="flex items-center justify-center space-x-8">
                    <div
                        v-for="step in steps"
                        :key="step.number"
                        class="flex items-center space-x-3"
                    >
                        <div
                            class="flex items-center justify-center w-12 h-12 rounded-full border-2 transition-all"
                            :class="{
                                'bg-blue-600 border-blue-600 text-white': currentStep >= step.number,
                                'border-gray-300 text-gray-400': currentStep < step.number
                            }"
                        >
                            <component :is="step.icon" class="w-5 h-5" />
                        </div>
                        <div class="hidden md:block">
                            <p class="text-sm font-medium" :class="currentStep >= step.number ? 'text-blue-600' : 'text-gray-400'">
                                {{ step.title }}
                            </p>
                            <p class="text-xs text-gray-500">{{ step.description }}</p>
                        </div>
                        <ChevronRight v-if="step.number < totalSteps" class="w-5 h-5 text-gray-400" />
                    </div>
                </div>
            </div>

            <Separator />

            <div class="min-h-96">
                <div v-if="currentStep === 1" class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-2xl font-semibold text-gray-900">Montant du transfert</h2>
                        <p class="text-gray-600 mt-2">Choisissez le montant et la devise</p>
                    </div>

                    <div class="max-w-md mx-auto space-y-6">
                        <div class="space-y-2">
                            <Label for="amount" class="text-lg">Montant</Label>
                            <Input
                                id="amount"
                                v-model.number="form.amount"
                                placeholder="0.00"
                                type="number"
                                step="0.01"
                                min="0"
                                class="text-xl h-12"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="currency" class="text-lg">Devise</Label>
                            <Select v-model="form.currency">
                                <SelectTrigger class="h-12">
                                    <SelectValue placeholder="Sélectionnez une devise" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="currency in currencies"
                                        :key="currency.symbol"
                                        :value="currency.symbol"
                                    >
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg">{{ getCurrencyFlag(currency.symbol) }}</span>
                                            <span>{{ currency.symbol }} - {{ currency.name }}</span>
                                        </div>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div v-if="form.amount > 0 && form.currency && form.currency !== 'EUR'" class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-blue-600">
                                    Équivalent en euros
                                </div>
                                <div v-if="exchangeData.loading" class="text-sm text-blue-500">
                                    Calcul en cours...
                                </div>
                                <div v-else class="text-sm font-semibold text-blue-800">
                                    1 {{ form.currency }} = {{ exchangeData.rate.toFixed(4) }} EUR
                                </div>
                            </div>
                            <div class="mt-2 text-2xl font-bold text-blue-900 flex items-center gap-2">
                                <span>🇪🇺</span>
                                <span>{{ exchangeData.convertedAmount.toFixed(2) }} EUR</span>
                            </div>
                        </div>

                        <div v-else-if="form.amount > 0 && form.currency === 'EUR'" class="rounded-lg border border-green-200 bg-green-50 p-4">
                            <div class="text-2xl font-bold text-green-900 flex items-center gap-2 justify-center">
                                <span>🇪🇺</span>
                                <span>{{ form.amount.toFixed(2) }} EUR</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="currentStep === 2" class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-2xl font-semibold text-gray-900">Destinataire</h2>
                        <p class="text-gray-600 mt-2">Saisissez l'IBAN du destinataire</p>
                    </div>

                    <div class="max-w-md mx-auto space-y-6">
                        <div class="space-y-2">
                            <Label for="iban" class="text-lg">IBAN du destinataire</Label>
                            <Input
                                id="iban"
                                v-model="form.recipient_iban"
                                placeholder="FR76 1234 5678 9012 3456 789 12"
                                class="text-lg h-12 font-mono"
                            />
                            <p v-if="ibanSearchLoading" class="text-sm text-blue-600">
                                Recherche en cours...
                            </p>
                        </div>

                        <div v-if="recipientUser" class="rounded-lg border border-green-200 bg-green-50 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <User class="w-6 h-6 text-green-600" />
                                </div>
                                <div>
                                    <p class="font-semibold text-green-900">{{ recipientUser.name }}</p>
                                    <p class="text-sm text-green-700">{{ recipientUser.email }}</p>
                                    <p class="text-xs text-green-600 font-mono">{{ recipientUser.iban }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="form.recipient_iban && !ibanSearchLoading" class="rounded-lg border border-red-200 bg-red-50 p-4">
                            <p class="text-red-700 text-center">Aucun utilisateur trouvé avec cet IBAN</p>
                        </div>
                    </div>
                </div>

                <div v-if="currentStep === 3" class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-2xl font-semibold text-gray-900">Confirmation</h2>
                        <p class="text-gray-600 mt-2">Vérifiez les détails du transfert</p>
                    </div>

                    <div class="max-w-md mx-auto space-y-6">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant</span>
                                <span class="font-semibold">{{ form.amount }} {{ form.currency }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Destinataire</span>
                                <span class="font-semibold">{{ recipientUser?.name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">IBAN</span>
                                <span class="font-mono text-sm">{{ recipientUser?.iban }}</span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="message" class="text-lg">Message (optionnel)</Label>
                            <Input
                                id="message"
                                v-model="form.message"
                                placeholder="Message pour le destinataire"
                                class="h-12"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between pt-6 border-t">
                <Button
                    v-if="currentStep > 1"
                    variant="outline"
                    @click="prevStep"
                    class="flex items-center gap-2"
                >
                    <ChevronLeft class="w-4 h-4" />
                    Précédent
                </Button>
                <div v-else></div>

                <Button
                    v-if="currentStep < totalSteps"
                    @click="nextStep"
                    :disabled="(currentStep === 1 && !canProceedFromStep1) || (currentStep === 2 && !canProceedFromStep2)"
                    class="flex items-center gap-2"
                >
                    Suivant
                    <ChevronRight class="w-4 h-4" />
                </Button>

                <Button
                    v-if="currentStep === totalSteps"
                    @click="submitTransfer"
                    :disabled="form.processing"
                    class="flex items-center gap-2"
                >
                    <ArrowRightLeft class="w-4 h-4" />
                    {{ form.processing ? 'Envoi...' : 'Envoyer le transfert' }}
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
