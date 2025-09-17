<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import StreakCard from '@/components/StreakCard.vue';
// import { usePage } from '@inertiajs/vue3'

const page = usePage()

type BestScore = { member: string; score: number } | null;
type Stats = {
    gamesPlayed: number;
    bestScore: BestScore;
    participationRate: number;
    mostActive: string | null;
    window: { days: number; from: string; to: string };
};
type FavoriteGame = { type: 'system' | 'custom'; id: number; name: string };

const familyDailyStreak = computed(() => (page.props as any).streaks?.family_daily ?? 0);
const familyWeeklyStreak = computed(() => (page.props as any).streaks?.family_weekly ?? 0);



// pull from Inertia props
const user = computed(() => (page.props as any).auth.user as { id: number; name: string; family_name?: string });
const stats = computed(() => (page.props as any).stats as Stats);
const favoriteGames = computed(() => (page.props as any).favoriteGames as FavoriteGame[]);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

// optional: wire your Start Game action (adjust route name to your app)
function startGame(game: FavoriteGame) {
    window.location.href = `/start-game/${game.id}/${game.type}`;
}
// (...)
</script>

<template>
    <div>
        <Head title="Dashboard" />
        <AppLayout :breadcrumbs="breadcrumbs">
            <div class="flex flex-col gap-6">
                <!-- Central Block with Family Name + Stats -->
                <div class="rounded-xl bg-white p-6 shadow-md dark:bg-gray-900">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-black dark:text-white">
                                {{ user.family_name || 'Your Family' }}
                            </h2>
                            <p class="text-gray-500 dark:text-gray-300">Welcome back, {{ user.name }}!</p>
                        </div>
                        <img src="/penguins.jpg" alt="Family" class="h-16 w-16 rounded-full border" />
                    </div>

                    

                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                        <StreakCard title="Family Daily Streak" :streak="familyDailyStreak" />
<StreakCard title="Family Weekly Streak" :streak="familyWeeklyStreak" />

</div>

                    <!-- Stats Grid -->
                    
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                        <div class="rounded bg-blue-100 p-4 text-center text-blue-900 dark:text-blue-400">
                            <h3 class="text-lg font-semibold">Games Played</h3>
                            <p class="text-3xl font-bold">{{ stats.gamesPlayed }}</p>
                        </div>

                        <div class="rounded bg-green-100 p-4 text-center text-green-900 dark:text-green-400">
                            <h3 class="text-lg font-semibold">Best Score</h3>
                            <p class="text-xl">
                                <span v-if="stats.bestScore">{{ stats.bestScore.member }} ({{ stats.bestScore.score }} pts)</span>
                                <span v-else>—</span>
                            </p>
                        </div>

                        <div class="rounded bg-yellow-100 p-4 text-center text-yellow-900 dark:text-yellow-300">
                            <h3 class="text-lg font-semibold">Participation</h3>
                            <p class="text-2xl font-bold">{{ stats.participationRate }}%</p>
                            <p class="text-xs opacity-70">last {{ stats.window.days }} days</p>
                        </div>

                        <div class="rounded bg-purple-100 p-4 text-center text-purple-900 dark:text-purple-500">
                            <h3 class="text-lg font-semibold">Most Active</h3>
                            <p class="text-xl">{{ stats.mostActive ?? '—' }}</p>
                            <p class="text-xs opacity-70">last {{ stats.window.days }} days</p>
                        </div>
                    </div>
                </div>

                <!-- Favorite Games List -->
                <div class="mt-8 rounded-xl bg-white p-6 shadow-md dark:bg-gray-900">
                    <h3 class="mb-4 text-xl font-bold text-black dark:text-white">Favorite Games</h3>

                    <div v-if="favoriteGames.length" class="space-y-4">
                        <div
                            v-for="g in favoriteGames"
                            :key="`${g.type}-${g.id}`"
                            class="flex items-center justify-between border-b border-gray-300 pb-2 dark:border-gray-700"
                        >
                            <div class="flex items-center gap-3">
                                <span class="text-yellow-400">★</span>
                                <span class="text-black dark:text-white">{{ g.name }}</span>
                                <span class="text-xs opacity-60">({{ g.type }})</span>
                            </div>
                            <button @click="startGame(g)" class="rounded bg-blue-600 px-4 py-1 text-white hover:bg-blue-700">Start Game</button>
                        </div>
                    </div>

                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">No favorites yet. Mark some games as favorites to see them here.</p>
                </div>
            </div>
        </AppLayout>
    </div>
</template>
