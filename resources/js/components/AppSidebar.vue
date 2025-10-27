<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, ArrowRightLeft } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = page.props.auth?.user;

const userNavItems: NavItem[] = [
    {
        title: 'Tableau de bord',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Échanges',
        href: '/transfer',
        icon: ArrowRightLeft,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Gestion des échanges',
        href: '/admin/exchanges',
        icon: ArrowRightLeft,
    },
    {
        title: 'Transferts',
        href: '/transfer',
        icon: ArrowRightLeft,
    },
    {
        title: 'Utilisateurs',
        href: '/admin/users',
        icon: Users,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="user?.role === 'ROLE_ADMIN' ? '/admin/exchanges' : dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain
                v-if="user?.role === 'ROLE_ADMIN'"
                :items="adminNavItems"
                title="Administration"
            />
            <NavMain
                v-else
                :items="userNavItems"
                title="Navigation"
            />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
