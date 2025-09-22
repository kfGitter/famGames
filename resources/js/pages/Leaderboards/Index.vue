<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    mostPlays: { family_member_id: number; plays: number; member: { id: number; name: string; avatar?: string | null } }[];
    mostWins: { family_member_id: number; wins: number; member: { id: number; name: string; avatar?: string | null } }[];
    personalBests: { family_member_id: number; best_score: number; member: { id: number; name: string; avatar?: string | null } }[];
    gameRecords: { game_id: number; title: string; record: number }[];
}>();
</script>

<template>
    <AppLayout>
        <div class="space-y-8 p-6 text-gray-900 dark:text-gray-100">
            <h1 class="text-3xl font-bold">Family Leaderboards 🏆</h1>

            <!-- Most Plays Section -->
            <section class="rounded-xl bg-indigo-50 p-4 shadow-sm dark:bg-indigo-900">
                <h2 class="mb-3 flex items-center gap-2 text-xl font-semibold">🎲 Most Plays</h2>
                <div class="grid gap-3">
                    <div
                        v-for="(r, index) in props.mostPlays"
                        :key="r.family_member_id"
                        class="flex transform items-center justify-between rounded-xl bg-white p-3 shadow transition hover:scale-105 hover:shadow-md dark:bg-gray-800"
                    >
                        <div class="flex items-center gap-3">
                            <!-- Avatar or initials -->
                            <div
                                class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-200 font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-200"
                            >
                                <img v-if="r.member?.avatar" :src="r.member.avatar" class="h-10 w-10 object-cover" />
                                <span v-else>{{ r.member?.name[0] }}</span>
                            </div>
                            <span class="font-medium">{{ r.member?.name }}</span>
                            <!-- Top 3 badge -->
                            <span v-if="index === 0" class="text-yellow-400">🥇</span>
                            <span v-else-if="index === 1" class="text-gray-400">🥈</span>
                            <span v-else-if="index === 2" class="text-orange-500">🥉</span>
                        </div>
                        <span class="font-semibold text-indigo-600 dark:text-indigo-300">{{ r.plays }} plays</span>
                    </div>
                </div>
            </section>

            <!-- Most Wins Section -->
            <section class="rounded-xl bg-green-50 p-4 shadow-sm dark:bg-green-900">
                <h2 class="mb-3 flex items-center gap-2 text-xl font-semibold">🎯 Most Wins</h2>
                <div class="grid gap-3">
                    <div
                        v-for="(r, index) in props.mostWins"
                        :key="r.family_member_id"
                        class="flex transform items-center justify-between rounded-xl bg-white p-3 shadow transition hover:scale-105 hover:shadow-md dark:bg-gray-800"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-200 font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-200"
                            >
                                <img v-if="r.member?.avatar" :src="r.member.avatar" class="h-10 w-10 object-cover" />
                                <span v-else>{{ r.member?.name[0] }}</span>
                            </div>
                            <span class="font-medium">{{ r.member?.name }}</span>
                            <span v-if="index === 0" class="text-yellow-400">🥇</span>
                            <span v-else-if="index === 1" class="text-gray-400">🥈</span>
                            <span v-else-if="index === 2" class="text-orange-500">🥉</span>
                        </div>
                        <span class="font-semibold text-green-600 dark:text-green-300">{{ r.wins }} wins</span>
                    </div>
                </div>
            </section>

            <!-- Personal Bests Section -->
            <section class="rounded-xl bg-yellow-50 p-4 shadow-sm dark:bg-yellow-900">
                <h2 class="mb-3 flex items-center gap-2 text-xl font-semibold">🏅 Personal Bests</h2>
                <div class="grid gap-3">
                    <div
                        v-for="(r, index) in props.personalBests"
                        :key="r.family_member_id"
                        class="flex transform items-center justify-between rounded-xl bg-white p-3 shadow transition hover:scale-105 hover:shadow-md dark:bg-gray-800"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-200 font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-200"
                            >
                                <img v-if="r.member?.avatar" :src="r.member.avatar" class="h-10 w-10 object-cover" />
                                <span v-else>{{ r.member?.name[0] }}</span>
                            </div>
                            <span class="font-medium">{{ r.member?.name }}</span>
                            <span v-if="index === 0" class="text-yellow-400">🥇</span>
                            <span v-else-if="index === 1" class="text-gray-400">🥈</span>
                            <span v-else-if="index === 2" class="text-orange-500">🥉</span>
                        </div>
                        <span class="font-semibold text-yellow-600 dark:text-yellow-300">{{ r.best_score }} pts</span>
                    </div>
                </div>
            </section>

            <!-- Game Records Section -->
            <section class="rounded-xl bg-pink-50 p-4 shadow-sm dark:bg-pink-900">
                <h2 class="mb-3 flex items-center gap-2 text-xl font-semibold">🔥 Game Records</h2>
                <div class="grid gap-3">
                    <div
                        v-for="g in props.gameRecords"
                        :key="g.game_id"
                        class="flex transform items-center justify-between rounded-xl bg-white p-3 shadow transition hover:scale-105 hover:shadow-md dark:bg-gray-800"
                    >
                        <span class="font-medium">{{ g.title }}</span>
                        <span class="font-semibold text-pink-600 dark:text-pink-300">{{ g.record }} pts</span>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
