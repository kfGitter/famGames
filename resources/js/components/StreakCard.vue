<template>
  <div class="rounded-lg bg-indigo-50 dark:bg-indigo-900 p-4 text-center">
    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ title }}</h3>
    <p class="mt-2 text-3xl font-bold text-indigo-800 dark:text-indigo-100">
      {{ streak ? streak.current : 0 }} 🔥
    </p>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string,
  streak: { current: number; last_played?: string } | null
}>();
</script>
