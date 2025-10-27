<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, ArrowRightLeft, TrendingUp, Calendar, Euro } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface ExchangeUser {
    id: number;
    name: string;
    email: string;
}

interface Exchange {
    id: number;
    sender: ExchangeUser;
    receiver: ExchangeUser;
    amount: number;
    currency: string;
    exchange_rate: number;
    amount_eur: number;
    message: string | null;
    created_at: string;
    date: string;
    time: string;
}

interface Props {
    exchanges: {
        data: Exchange[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters: {
        search: string;
    };
    stats: {
        total_exchanges: number;
        total_amount: number;
        today_exchanges: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Administration',
        href: '/admin/users'
    },
    {
        title: 'Échanges',
        href: '/admin/exchanges'
    }
];

const search = ref(props.filters.search);

watch(search, (value) => {
    router.get('/admin/exchanges', { search: value }, {
        preserveState: true,
        replace: true,
    });
}, { debounceTime: 300 });

const formatAmount = (amount: number, currency: string) => {
    return `${amount.toFixed(2)} ${currency}`;
};

const formatEurAmount = (amount: number) => {
    return `${amount.toFixed(2)} €`;
};

const getCurrencyBadgeVariant = (currency: string) => {
    switch (currency) {
        case 'EUR': return 'default';
        case 'USD': return 'secondary';
        case 'GBP': return 'outline';
        default: return 'secondary';
    }
};
</script>

<template>
    <Head title="Administration - Échanges" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Échanges</h1>
                    <p class="text-muted-foreground">
                        Surveillez toutes les transactions de la plateforme
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Badge variant="outline" class="flex items-center gap-2">
                        <ArrowRightLeft class="h-4 w-4" />
                        {{ props.exchanges.total }} échanges
                    </Badge>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total échanges</CardTitle>
                        <ArrowRightLeft class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats.total_exchanges }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Volume total (EUR)</CardTitle>
                        <Euro class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatEurAmount(props.stats.total_amount) }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Aujourd'hui</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats.today_exchanges }}</div>
                        <p class="text-xs text-muted-foreground">échanges</p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Rechercher des échanges</CardTitle>
                    <CardDescription>
                        Recherchez par nom d'utilisateur, email, montant ou devise
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="relative">
                        <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Rechercher un échange..."
                            class="pl-10"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Liste des échanges</CardTitle>
                    <CardDescription>
                        {{ props.exchanges.total }} échanges trouvés
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Expéditeur</TableHead>
                                <TableHead>Destinataire</TableHead>
                                <TableHead>Montant</TableHead>
                                <TableHead>Devise</TableHead>
                                <TableHead>Taux</TableHead>
                                <TableHead>Équivalent EUR</TableHead>
                                <TableHead>Message</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="exchange in props.exchanges.data" :key="exchange.id">
                                <TableCell>
                                    <div>
                                        <div class="font-medium">{{ exchange.sender.name }}</div>
                                        <div class="text-sm text-muted-foreground">{{ exchange.sender.email }}</div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div>
                                        <div class="font-medium">{{ exchange.receiver.name }}</div>
                                        <div class="text-sm text-muted-foreground">{{ exchange.receiver.email }}</div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <span class="font-medium">{{ formatAmount(exchange.amount, exchange.currency) }}</span>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getCurrencyBadgeVariant(exchange.currency)">
                                        {{ exchange.currency }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm font-mono">{{ exchange.exchange_rate.toFixed(4) }}</span>
                                </TableCell>
                                <TableCell>
                                    <span class="font-medium text-green-600">{{ formatEurAmount(exchange.amount_eur) }}</span>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm max-w-[200px] truncate block">
                                        {{ exchange.message || '-' }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <div class="text-sm">
                                        <div class="font-medium">{{ exchange.date }}</div>
                                        <div class="text-muted-foreground">{{ exchange.time }}</div>
                                    </div>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="`/admin/users/${exchange.sender.id}`">
                                            <Badge variant="ghost" class="cursor-pointer hover:bg-gray-100">
                                                Voir expéditeur
                                            </Badge>
                                        </Link>
                                        <Link :href="`/admin/users/${exchange.receiver.id}`">
                                            <Badge variant="ghost" class="cursor-pointer hover:bg-gray-100">
                                                Voir destinataire
                                            </Badge>
                                        </Link>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div class="flex items-center justify-between space-x-2 py-4" v-if="props.exchanges.last_page > 1">
                        <div class="text-sm text-muted-foreground">
                            Page {{ props.exchanges.current_page }} sur {{ props.exchanges.last_page }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <Link
                                v-for="link in props.exchanges.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-2 text-sm border rounded',
                                    link.active
                                        ? 'bg-primary text-primary-foreground border-primary'
                                        : 'bg-background hover:bg-accent border-border',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                :disabled="!link.url"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>