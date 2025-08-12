<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';

const { props } = usePage();
const user = props.auth.user;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6">
            <!-- Central Block with Family Name + Stats -->
            <div class="rounded-xl bg-white p-6 shadow-md dark:bg-gray-900">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-black dark:text-white">
                            {{ user.family_name }}
                        </h2>
                        <p class="text-gray-500 dark:text-gray-300">
                            Welcome back, {{ user.name }}!
                        </p>
                    </div>
                    <img src="/penguins.jpg" alt="Family" class="h-16 w-16 rounded-full border" />
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div class="rounded bg-blue-100 p-4 text-center text-blue-900 dark:text-blue-400">
                        <h3 class="text-lg font-semibold">Games Played</h3>
                        <p class="text-3xl font-bold">12</p>
                    </div>
                    <div class="rounded bg-green-100 p-4 text-center text-green-900 dark:text-green-400">
                        <h3 class="text-lg font-semibold">Best Score</h3>
                        <p class="text-xl">Amina (120 pts)</p>
                    </div>
                    <div class="rounded bg-yellow-100 p-4 text-center text-yellow-900 dark:text-yellow-300">
                        <h3 class="text-lg font-semibold">Participation</h3>
                        <p class="text-2xl font-bold">85%</p>
                    </div>
                    <div class="rounded bg-purple-100 p-4 text-center text-purple-900 dark:text-purple-500">
                        <h3 class="text-lg font-semibold">Most Active</h3>
                        <p class="text-xl">Tommy</p>
                    </div>
                </div>
            </div>

            <!-- Favorite Games List -->
            <div class="mt-8 rounded-xl bg-white p-6 shadow-md dark:bg-gray-900">
                <h3 class="mb-4 text-xl font-bold text-black dark:text-white"> Favorite Games</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b pb-2 border-gray-300 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <span class="text-yellow-400">★</span>
                            <span class="text-black dark:text-white">Charades</span>
                        </div>
                        <button class="rounded bg-blue-600 px-4 py-1 text-white hover:bg-blue-700">Start Game</button>
                    </div>
                    <div class="flex items-center justify-between border-b pb-2 border-gray-300 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <span class="text-yellow-400">★</span>
                            <span class="text-black dark:text-white">Heads Up</span>
                        </div>
                        <button class="rounded bg-blue-600 px-4 py-1 text-white hover:bg-blue-700">Start Game</button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-yellow-400">★</span>
                            <span class="text-black dark:text-white">Family Quiz</span>
                        </div>
                        <button class="rounded bg-blue-600 px-4 py-1 text-white hover:bg-blue-700">Start Game</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
