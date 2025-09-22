<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
  member: { id: number; name: string; avatar?: string | null };
  achievements: { id: number; name: string; icon?: string | null; description?: string | null; code: string; awarded_at: string }[];
}>();

const goBack = () => window.history.back();
</script>

<template>
  <AppLayout>
    <div class="space-y-6 p-6 text-gray-900 dark:text-gray-100">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ member.name }}’s Achievements</h1>
        <button 
          @click="goBack" 
          class="px-3 py-1 rounded-lg bg-gray-300 text-black hover:bg-gray-400 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600"
        >
          ← Back
        </button>
      </div>

      <!-- Encouragement -->
      <div class="text-sm italic text-gray-600 dark:text-gray-400">
        Keep going, {{ member.name }}! Collect more achievements and share fun moments with your family!
      </div>

      <!-- Achievements grid -->
      <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        <div
          v-for="a in achievements"
          :key="a.id"
          class="flex flex-col items-center text-center rounded-xl bg-white dark:bg-gray-800 p-4 shadow hover:shadow-md transition group"
        >
          <!-- <div class="mb-2 text-4xl">
            <span v-if="a.icon">{{ a.icon }}</span>
            <span v-else>🏆</span>
          </div> -->
          
          <div class="font-medium">{{ a.name }}</div>
          <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 opacity-80 group-hover:opacity-100">{{ a.description }}</div>
          <div class="text-[10px] text-gray-400 mt-1">Awarded: {{ new Date(a.awarded_at).toLocaleDateString() }}</div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
