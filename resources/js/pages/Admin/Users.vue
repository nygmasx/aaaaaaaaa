<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, Users, Shield, Ban, Eye } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    iban: string;
    balance: number;
    blocked?: boolean;
    created_at: string;
    sent_exchanges_count: number;
    received_exchanges_count: number;
}

interface Props {
    users: {
        data: User[];
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
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Administration',
        href: '/admin/users'
    },
    {
        title: 'Utilisateurs',
        href: '/admin/users'
    }
];

const search = ref(props.filters.search);

watch(search, (value) => {
    router.get('/admin/users', { search: value }, {
        preserveState: true,
        replace: true,
    });
}, { debounceTime: 300 });

const toggleAdmin = (userId: number) => {
    router.post(`/admin/users/${userId}/toggle-admin`, {}, {
        preserveState: true,
        onSuccess: () => {
            // Optionally show a toast notification
        }
    });
};

const toggleBlock = (userId: number) => {
    router.post(`/admin/users/${userId}/toggle-block`, {}, {
        preserveState: true,
        onSuccess: () => {
            // Optionally show a toast notification
        }
    });
};

const getRoleBadgeVariant = (role: string) => {
    return role === 'ROLE_ADMIN' ? 'default' : 'secondary';
};

const getStatusBadgeVariant = (blocked?: boolean) => {
    return blocked ? 'destructive' : 'default';
};
</script>

<template>
    <Head title="Administration - Utilisateurs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Utilisateurs</h1>
                    <p class="text-muted-foreground">
                        Gérez les utilisateurs de la plateforme
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Badge variant="outline" class="flex items-center gap-2">
                        <Users class="h-4 w-4" />
                        {{ props.users.total }} utilisateurs
                    </Badge>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total utilisateurs</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.users.total }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Administrateurs</CardTitle>
                        <Shield class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ props.users.data.filter(user => user.role === 'ROLE_ADMIN').length }}
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Utilisateurs bloqués</CardTitle>
                        <Ban class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ props.users.data.filter(user => user.blocked).length }}
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Rechercher des utilisateurs</CardTitle>
                    <CardDescription>
                        Recherchez par nom, email ou IBAN
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="relative">
                        <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Rechercher un utilisateur..."
                            class="pl-10"
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Liste des utilisateurs</CardTitle>
                    <CardDescription>
                        {{ props.users.total }} utilisateurs trouvés
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Utilisateur</TableHead>
                                <TableHead>IBAN</TableHead>
                                <TableHead>Solde</TableHead>
                                <TableHead>Rôle</TableHead>
                                <TableHead>Statut</TableHead>
                                <TableHead>Échanges</TableHead>
                                <TableHead>Inscription</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="user in props.users.data" :key="user.id">
                                <TableCell>
                                    <div>
                                        <div class="font-medium">{{ user.name }}</div>
                                        <div class="text-sm text-muted-foreground">{{ user.email }}</div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <code class="text-xs">{{ user.iban }}</code>
                                </TableCell>
                                <TableCell>
                                    <span class="font-medium">{{ user.balance.toFixed(2) }} €</span>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getRoleBadgeVariant(user.role)">
                                        {{ user.role === 'ROLE_ADMIN' ? 'Admin' : 'Utilisateur' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge :variant="getStatusBadgeVariant(user.blocked)">
                                        {{ user.blocked ? 'Bloqué' : 'Actif' }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div class="text-sm">
                                        <div>Envoyés: {{ user.sent_exchanges_count }}</div>
                                        <div>Reçus: {{ user.received_exchanges_count }}</div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <span class="text-sm">{{ new Date(user.created_at).toLocaleDateString('fr-FR') }}</span>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="`/admin/users/${user.id}`">
                                            <Button variant="ghost" size="sm">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            @click="toggleAdmin(user.id)"
                                        >
                                            <Shield class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            @click="toggleBlock(user.id)"
                                        >
                                            <Ban class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div class="flex items-center justify-between space-x-2 py-4" v-if="props.users.last_page > 1">
                        <div class="text-sm text-muted-foreground">
                            Page {{ props.users.current_page }} sur {{ props.users.last_page }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <Link
                                v-for="link in props.users.links"
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