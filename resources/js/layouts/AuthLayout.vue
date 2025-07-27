<script setup lang="ts">
// import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue';
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-blue-50 px-4">
        <!-- Logo or title -->
        <div class="mb-8 text-center">
            <img src="/famlogo.png" alt="FamGames" class="mx-auto mb-2 h-20 w-20" />
            <h1 class="text-3xl text-blue-400 font-bold">FAMILY PLAY</h1>
        </div>

        <!-- Main content (login/register forms) -->
        <div class="w-full max-w-md rounded bg-white p-6 shadow">
            <h2 class="mb-2 text-center text-xl font-semibold text-blue-800">{{ title }}</h2>
            <p v-if="description" class="mb-4 text-center text-sm text-gray-600">{{ description }}</p>
            <slot />
        </div>
    </div>
</template>

