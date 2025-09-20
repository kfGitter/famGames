<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    member: { id: number; name: string; avatar?: string | null };
    achievements: { id: number; name: string; icon?: string | null; description?: string | null; code: string; awarded_at: string }[];
    // familyAchievements: { id: number; name: string; icon?: string | null; description?: string | null; code: string; awarded_at: string }[];
}>();

// const props = defineProps<{
//   member: { id: number; name: string; avatar?: string|null };
//   achievements: { id: number; name: string; icon?: string|null; description?: string|null; code: string }[];
// }>();

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">{{ member.name }}’s Achievements</h1>

                <button @click="goBack" class="bg-gray-300 text-black hover:bg-gray-400">← Back</button>
            </div>

            <div class="mt-2 text-sm text-gray-600 italic dark:text-gray-400">
                Keep going, {{ member.name }}! Every achievement brings your family closer
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                <div v-for="a in achievements" :key="a.id" class="group relative flex flex-col items-center rounded border p-4 text-center">
                    <!-- <div class="mb-2 text-3xl">
                        <span v-if="a.icon">{{ a.icon }}</span>
                        <span v-else>🏆</span>
                    </div> -->
                    <div class="font-medium">{{ a.name }}</div>
                    <div class="mt-1 text-xs text-gray-500 opacity-80 group-hover:opacity-100" :title="a.description">{{ a.description }}</div>
                </div>
            </div>

            <!-- Family Achievements -->
            <!-- <div v-if="props.familyAchievements.length" class="mt-6">
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
            </div> -->
        </div>
    </AppLayout>
</template>
