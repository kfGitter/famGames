<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    member: { id: number; name: string; avatar?: string | null };
    stats: { totalPlayed: number; wins: number };
    topScores: { game_id: number; game_title: string; best_score: number }[];
    recordsHeld: { game_title: string; record_score: number }[];
    achievements: { id: number; code: string; name: string; icon?: string | null; description?: string | null; awarded_at: string }[];
}>();
</script>

<template>
    <AppLayout>
        <div class="space-y-6 p-6 text-black">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 overflow-hidden rounded-full bg-gray-200">
                    <img v-if="member.avatar" :src="`/storage/${member.avatar}`" class="h-full w-full object-cover" />
                </div>
                <h1 class="text-2xl font-bold">{{ member.name }}’s Profile</h1>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="rounded border p-4">
                    <div class="text-sm text-gray-500">Total Games Played</div>
                    <div class="text-2xl font-semibold">{{ stats.totalPlayed }}</div>
                </div>
                <div class="rounded border p-4">
                    <div class="text-sm text-gray-500">Games Won</div>
                    <div class="text-2xl font-semibold">{{ stats.wins }}</div>
                </div>
            </div>

            <section>
                <h2 class="mb-2 text-xl font-semibold">Top Scores</h2>
                <ul class="space-y-1">
                    <li v-for="t in topScores" :key="t.game_id" class="flex justify-between rounded border p-2">
                        <span>{{ t.game_title }}</span>
                        <span>{{ t.best_score }} pts</span>
                    </li>
                </ul>
            </section>

            <section>
                <h2 class="mb-2 text-xl font-semibold">Holds Record For</h2>
                <div v-if="recordsHeld.length === 0" class="text-gray-500">No records yet.</div>
                <ul v-else class="space-y-1">
                    <li v-for="r in recordsHeld" :key="r.game_title" class="flex justify-between rounded border p-2">
                        <span>{{ r.game_title }}</span>
                        <span>{{ r.record_score }} pts</span>
                    </li>
                </ul>
            </section>

            <section>
                <div class="flex items-center justify-between">
                    <h2 class="mb-2 text-xl font-semibold">Stickers (Achievements)</h2>
                    <!-- <a class="text-blue-600 hover:underline" href="#">View Stickers</a> -->
                </div>
                <div class="flex flex-wrap gap-2">
                    <div v-for="a in achievements" :key="a.id" class="rounded border p-2">
                        <div class="font-medium">{{ a.name }}</div>
                        <div class="text-xs text-gray-500">{{ a.description }}</div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
