<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
  member: { id: number; name: string; avatar?: string|null };
  achievements: { id: number; name: string; icon?: string|null; description?: string|null; code: string }[];
}>();

const goBack = () => {
    window.history.back();
};

</script>

<template>
  <AppLayout>
    <div class="p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ member.name }}’s Stickers</h1>
            <button @click="goBack" class="bg-gray-300 text-black hover:bg-gray-400"> ← Back </button>
      </div>


      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="a in achievements" :key="a.id" class="border rounded p-4 flex flex-col items-center text-center">
          <div class="text-3xl mb-2">
            <span v-if="a.icon">{{ a.icon }}</span>
            <span v-else>🏆</span>
          </div>
          <div class="font-medium">{{ a.name }}</div>
          <div class="text-xs text-gray-500">{{ a.description }}</div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
