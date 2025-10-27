<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard, transfer } from '@/routes';
import { type BreadcrumbItem, type TransferData, type RecentTransfer } from '@/types';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { AreaChart } from '@/components/ui/chart-area';
import { Button } from '@/components/ui/button';

interface Props {
    transfersData?: TransferData[];
    recentTransfers?: RecentTransfer[];
    totalTransfers?: number;
}

const props = withDefaults(defineProps<Props>(), {
    transfersData: () => [],
    recentTransfers: () => [],
    totalTransfers: 0,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url
    }
];

const page = usePage();

const user = page.props.auth.user;

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Card
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <CardHeader>
                        <CardTitle>Solde du compte</CardTitle>
                        <CardDescription>Personnel - EUR</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-3xl font-bold">{{ user.balance }} €</p>
                    </CardContent>
                </Card>
                <Card
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <CardHeader>
                        <CardTitle>Nombre de transferts</CardTitle>
                        <CardDescription>Année 2025</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-3xl font-bold">{{ props.totalTransfers }}</p>
                    </CardContent>
                </Card>
                <Card
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <CardHeader>
                        <CardTitle>Actions rapides</CardTitle>
                        <CardDescription>Gérez vos finances</CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-2">
                        <Link :href="transfer().url">
                            <Button class="w-full">
                                Nouveau transfert
                            </Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <Card class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader>
                        <CardTitle>Transferts ce mois</CardTitle>
                        <CardDescription>Nombre de transferts effectués</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <AreaChart :data="props.transfersData" :categories="['transfers']" index="name" class="h-[300px]"/>
                    </CardContent>
                </Card>

                <Card class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader>
                        <CardTitle>Transferts récents</CardTitle>
                        <CardDescription>Vos dernières transactions</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="transfer in props.recentTransfers"
                                :key="transfer.id"
                                class="flex items-center justify-between p-3 rounded-lg border"
                            >
                                <div class="flex-1">
                                    <p class="font-medium">{{ transfer.recipient }}</p>
                                    <p class="text-sm text-gray-600">{{ transfer.date }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold">{{ transfer.amount }} {{ transfer.currency }}</p>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': transfer.status === 'Terminé',
                                            'bg-yellow-100 text-yellow-800': transfer.status === 'En cours',
                                            'bg-red-100 text-red-800': transfer.status === 'Échoué'
                                        }"
                                    >
                                        {{ transfer.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
