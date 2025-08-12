<script setup lang="ts">
import DarkModeToggle from '@/components/DarkModeToggle.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';


// import { Gamepad2, Medal, Users } from 'lucide-vue-next';
import {
    Gamepad2,
    List,
    Medal,
    //   Joystick,
    Trophy,
    //   BookOpen,
    //   Folder,
    // LayoutGrid,
    Users,
} from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Family Members',
        href: '/members',
        icon: Users,
    },
    {
        title: 'Game Library',
        href: '/games',
        icon: List,
    },
    {
        title: 'My Games',
        href: '/my-games',
        icon: Gamepad2,
    },
    {
        title: 'Achievements',
        href: '/achievements',
        icon: Medal,
    },
    {
        title: 'Leaderboards',
        href: '/leaderboards',
        icon: Trophy,
    },
];

// ------- Footer Navigation Items
// const footerNavItems: NavItem[] = [
//     {
//         title: 'Github Repo',
//         href: 'https://github.com/laravel/vue-starter-kit',
//         icon: Folder,
//     },
//     {
//         title: 'Documentation',
//         href: 'https://laravel.com/docs/starter-kits#vue',
//         icon: BookOpen,
//     },
// ];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- dark mode toggle -->

            <div class="sidebar-content flex flex-col justify-between">
                <div class="mt-auto p-4">
                    <DarkModeToggle />
                </div>
            </div>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
