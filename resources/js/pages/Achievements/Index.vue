<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
  members: { id: number; name: string; avatar?: string | null }[];
  familyAchievements: { id: number; name: string; icon?: string | null; description?: string | null; code: string; awarded_at: string }[];
}>();

function goToMember(memberId: number) {
  router.get(`/achievements/${memberId}`);
}
</script>

<template>
  <AppLayout>
    <div class="space-y-8 p-6 text-gray-900 dark:text-gray-100">
      <h1 class="text-3xl font-bold">Family Achievements ✨</h1>
      <!-- Family Members -->
      <section>
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2"> Family Members</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
          <div
            v-for="m in props.members"
            :key="m.id"
            class="flex flex-col items-center cursor-pointer bg-white dark:bg-gray-800 rounded-xl shadow p-4 hover:shadow-md hover:scale-105 transition"
            @click="goToMember(m.id)"
          >
            <div class="h-16 w-16 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-lg font-bold text-gray-600 dark:text-gray-200">
              <img v-if="m.avatar" :src="`/storage/${m.avatar}`" class="h-full w-full object-cover" />
              <span v-else>{{ m.name[0] }}</span>
            </div>
            <div class="mt-2 font-medium">{{ m.name }}</div>
          </div>
        </div>
      </section>

      <!-- Family Achievements -->
      <section v-if="props.familyAchievements.length">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">Shared Family Achievements 🤝</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
          <div
            v-for="a in props.familyAchievements"
            :key="a.id"
            class="flex flex-col items-center text-center bg-yellow-50 dark:bg-yellow-900 rounded-xl p-4 shadow hover:shadow-md transition"
          >
            <!-- Icon or fallback -->
            <!-- <div class="mb-2 text-4xl">
              <span v-if="a.icon">{{ a.icon }}</span>
              <span v-else>🏆</span>
            </div> -->
            
            <div class="font-medium">{{ a.name }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ a.description }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Awarded: {{ new Date(a.awarded_at).toLocaleDateString() }}</div>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>
