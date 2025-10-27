<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ArrowLeft, Shield, Ban, Users, TrendingUp, TrendingDown, ArrowUpRight, ArrowDownLeft } from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
    iban: string;
    balance: number;
    role: string;
    blocked: boolean;
    created_at: string;
    sent_exchanges_count: number;
    received_exchanges_count: number;
}

interface Exchange {
    id: number;
    type: 'sent' | 'received';
    other_user: string;
    other_user_email: string;
    amount: number;
    currency: string;
    exchange_rate: number;
    message: string | null;
    date: string;
}

interface Props {
    user: User;
    exchanges: Exchange[];
}

const props = defineProps<Props>();

const page = usePage();
const currentUser = page.props.auth.user;
const isCurrentUser = currentUser.id === props.user.id;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Administration',
        href: '/admin/users'
    },
    {
        title: 'Utilisateurs',
        href: '/admin/users'
    },
    {
        title: props.user.name,
        href: `/admin/users/${props.user.id}`
    }
];

const toggleAdmin = () => {
    router.post(`/admin/users/${props.user.id}/toggle-admin`, {}, {
        preserveState: true,
        onSuccess: () => {
            // Optionally show a toast notification
        }
    });
};

const toggleBlock = () => {
    router.post(`/admin/users/${props.user.id}/toggle-block`, {}, {
        preserveState: true,
        onSuccess: () => {
            // Optionally show a toast notification
        }
    });
};

const getRoleBadgeVariant = (role: string) => {
    return role === 'ROLE_ADMIN' ? 'default' : 'secondary';
};

const getStatusBadgeVariant = (blocked: boolean) => {
    return blocked ? 'destructive' : 'default';
};

const getExchangeIcon = (type: string) => {
    return type === 'sent' ? ArrowUpRight : ArrowDownLeft;
};

const getExchangeClass = (type: string) => {
    return type === 'sent'
        ? 'text-red-600 bg-red-50'
        : 'text-green-600 bg-green-50';
};

const formatAmount = (amount: number, currency: string) => {
    return `${amount.toFixed(2)} ${currency}`;
};

const totalSent = props.exchanges
    .filter(e => e.type === 'sent')
    .reduce((sum, e) => sum + (e.amount * e.exchange_rate), 0);

const totalReceived = props.exchanges
    .filter(e => e.type === 'received')
    .reduce((sum, e) => sum + (e.amount * e.exchange_rate), 0);
</script>

<template>
    <Head :title="`Administration - ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/admin/users">
                        <Button variant="ghost" size="sm">
                            <ArrowLeft class="h-4 w-4 mr-2" />
                            Retour
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">{{ user.name }}</h1>
                        <p class="text-muted-foreground">{{ user.email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Badge :variant="getRoleBadgeVariant(user.role)">
                        {{ user.role === 'ROLE_ADMIN' ? 'Administrateur' : 'Utilisateur' }}
                    </Badge>
                    <Badge :variant="getStatusBadgeVariant(user.blocked)">
                        {{ user.blocked ? 'Bloqué' : 'Actif' }}
                    </Badge>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Solde actuel</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ user.balance.toFixed(2) }} €</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Transferts envoyés</CardTitle>
                        <ArrowUpRight class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ user.sent_exchanges_count }}</div>
                        <p class="text-xs text-muted-foreground">
                            Total: {{ totalSent.toFixed(2) }} €
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Transferts reçus</CardTitle>
                        <ArrowDownLeft class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ user.received_exchanges_count }}</div>
                        <p class="text-xs text-muted-foreground">
                            Total: {{ totalReceived.toFixed(2) }} €
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Membre depuis</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ new Date(user.created_at).toLocaleDateString('fr-FR', {
                                day: 'numeric',
                                month: 'short'
                            }) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ user.created_at }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Informations du compte</CardTitle>
                    <CardDescription>Détails et actions d'administration</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <h4 class="font-medium mb-2">Informations personnelles</h4>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="font-medium">Nom:</span> {{ user.name }}
                                </div>
                                <div>
                                    <span class="font-medium">Email:</span> {{ user.email }}
                                </div>
                                <div>
                                    <span class="font-medium">IBAN:</span>
                                    <code class="ml-2 text-xs">{{ user.iban }}</code>
                                </div>
                                <div>
                                    <span class="font-medium">Solde:</span> {{ user.balance.toFixed(2) }} €
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Actions d'administration</h4>
                            <div class="flex flex-col gap-2">
                                <Button
                                    v-if="!isCurrentUser"
                                    @click="toggleAdmin"
                                    :variant="user.role === 'ROLE_ADMIN' ? 'destructive' : 'default'"
                                    size="sm"
                                >
                                    <Shield class="h-4 w-4 mr-2" />
                                    {{ user.role === 'ROLE_ADMIN' ? 'Retirer les droits admin' : 'Promouvoir admin' }}
                                </Button>
                                <Button
                                    v-if="!isCurrentUser"
                                    @click="toggleBlock"
                                    :variant="user.blocked ? 'default' : 'destructive'"
                                    size="sm"
                                >
                                    <Ban class="h-4 w-4 mr-2" />
                                    {{ user.blocked ? 'Débloquer' : 'Bloquer' }}
                                </Button>
                                <div v-if="isCurrentUser" class="text-sm text-muted-foreground">
                                    Vous ne pouvez pas modifier vos propres droits
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Historique des échanges</CardTitle>
                    <CardDescription>
                        {{ exchanges.length }} échange(s) au total
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="exchanges.length === 0" class="text-center py-8 text-muted-foreground">
                        Aucun échange trouvé pour cet utilisateur.
                    </div>
                    <Table v-else>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Type</TableHead>
                                <TableHead>Autre utilisateur</TableHead>
                                <TableHead>Montant</TableHead>
                                <TableHead>Taux de change</TableHead>
                                <TableHead>Message</TableHead>
                                <TableHead>Date</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="exchange in exchanges" :key="exchange.id">
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <div :class="[
                                            'p-2 rounded-full',
                                            getExchangeClass(exchange.type)
                                        ]">
                                            <component
                                                :is="getExchangeIcon(exchange.type)"
                                                class="h-4 w-4"
                                            />
                                        </div>
                                        <Badge :variant="exchange.type === 'sent' ? 'destructive' : 'default'">
                                            {{ exchange.type === 'sent' ? 'Envoyé' : 'Reçu' }}
                                        </Badge>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div>
                                        <div class="font-medium">{{ exchange.other_user }}</div>
                                        <div class="text-sm text-muted-foreground">{{ exchange.other_user_email }}</div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <span class="font-medium">
                                        {{ formatAmount(exchange.amount, exchange.currency) }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">{{ exchange.exchange_rate.toFixed(4) }}</span>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">
                                        {{ exchange.message || '-' }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">{{ exchange.date }}</span>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>