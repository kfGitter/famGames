<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();

const logoPath = '/famlogo.png';
</script>

<template>
    <div class="flex items-center space-x-2" v-bind="$attrs">
        <!-- Logo Image -->
        <img :src="logoPath" alt="FamGames Logo" class="h-15 w-15 object-contain" />

        <!-- App Name -->
        <span class="text-base font-semibold text-gray-800 dark:text-white">FamGames</span>
    </div>
</template>
