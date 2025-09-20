<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

// const props = defineProps<{
//     members: { id: number; name: string; avatar?: string | null }[];
// }>();


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
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-bold">Family Members</h1>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                <div
                    v-for="m in props.members"
                    :key="m.id"
                    class="flex cursor-pointer items-center gap-2 rounded border p-4 hover:bg-gray-100"
                    @click="goToMember(m.id)"
                >
                    <div class="h-12 w-12 overflow-hidden rounded-full bg-gray-200">
                        <img v-if="m.avatar" :src="`/storage/${m.avatar}`" class="h-full w-full object-cover" />
                    </div>
                    <div class="font-medium">{{ m.name }}</div>
                </div>
            </div>

            <!-- family achievements -->
             <div v-if="props.familyAchievements.length" class="mt-8">
  <h2 class="mb-2 text-xl font-bold">Family Achievements 🎉</h2>
  <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
    <div
      v-for="a in props.familyAchievements"
      :key="a.id"
      class="flex flex-col items-center rounded border bg-yellow-50 p-4 text-center dark:bg-yellow-900"
    >
      <div class="mb-2 text-3xl">
        <span v-if="a.icon">{{ a.icon }}</span>
        <span v-else>🏆</span>
      </div>
      <div class="font-medium">{{ a.name }}</div>
      <div class="text-xs text-gray-500 dark:text-gray-400">{{ a.description }}</div>
    </div>
  </div>
</div>

        </div>
    </AppLayout>
</template>
