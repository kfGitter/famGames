<script setup lang="ts">
import ActivityChart from '@/components/ActivityChart.vue';
import StreakCard from '@/components/StreakCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
// const user = computed(() => (page.props as any).auth.user);
const user = computed(() => (page.props as any).auth.user as { id: number; name: string; family_name?: string; avatar?: string });
const stats = computed(() => (page.props as any).stats);
const favoriteGames = computed(() => (page.props as any).favoriteGames ?? []);
const latestAchievements = computed(() => (page.props as any).latestAchievements ?? []);
const activeChallenges = computed(() => (page.props as any).activeChallenges ?? []);
const history = computed(() => (page.props as any).history ?? []);
const familyDailyStreak = computed(() => (page.props as any).streaks?.family_daily ?? { current: 0 });
const familyWeeklyStreak = computed(() => (page.props as any).streaks?.family_weekly ?? { current: 0 });

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

const selectedImage = ref<string | null>(null);
const file = ref<File | null>(null);

function onFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    const chosen = target.files?.[0];
    if (chosen) {
        file.value = chosen;
        selectedImage.value = URL.createObjectURL(chosen);

        const formData = new FormData();
        formData.append('avatar', chosen);

        router.post('/user/avatar', formData, {
            preserveScroll: true,
            onSuccess: () => {
                // Refresh user props from backend (so avatar persists)
                router.reload({ only: ['auth'] });
            },
        });
    }
}


function startGame(game: { type: string; id: number }) {
    window.location.href = `/start-game/${game.id}/${game.type}`;
}
</script>

<template>
    <div>
        <Head title="Dashboard" />
        <AppLayout :breadcrumbs="breadcrumbs">
            <div class="flex flex-col gap-6">
                <!-- Central Block -->
                <div class="rounded-xl bg-white p-6 shadow-md dark:bg-gray-900">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-black dark:text-white">{{ user.family_name || 'Your Family' }}</h2>
                            <p class="text-gray-500 dark:text-gray-300">Welcome back, {{ user.name }}!</p>
                        </div>
                        <!-- Avatar Upload -->
                        <div class="flex flex-col items-center">
                            <img
                                :src="selectedImage || (user.avatar ? '/storage/' + user.avatar : '/penguins.jpg')"
                                alt="Family"
                                class="mb-2 h-16 w-16 rounded-full border"
                            />
                            <input type="file" @change="onFileChange" accept="image/*" class="text-white" />
                        </div>
                    </div>

                    <!-- Streaks and Stats -->
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                        <StreakCard title="Family Daily Streak" :streak="familyDailyStreak" />
                        <StreakCard title="Family Weekly Streak" :streak="familyWeeklyStreak" />
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
                        <div class="rounded bg-blue-100 p-4 text-center text-blue-900 dark:text-blue-400">
                            <h3 class="text-lg font-semibold">Games Played</h3>
                            <p class="text-3xl font-bold">{{ stats.gamesPlayed }}</p>
                        </div>
                        <div class="rounded bg-green-100 p-4 text-center text-green-900 dark:text-green-400">
                            <h3 class="text-lg font-semibold">Best Score</h3>
                            <p class="text-xl">{{ stats.bestScore ? `${stats.bestScore.member} (${stats.bestScore.score} pts)` : '—' }}</p>
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

                <!-- Favorite Games -->
                <div class="rounded-xl bg-white p-6 shadow-md dark:bg-gray-900">
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

                <!-- Achievements -->
                <div class="rounded-xl bg-white p-6 shadow-md dark:bg-gray-900">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-black dark:text-white">Latest Family Achievements</h3>
                        <a href="/achievements" class="text-sm text-blue-600 hover:underline dark:text-blue-400">View All</a>
                    </div>
                    <div v-if="latestAchievements.length" class="space-y-4">
                        <div
                            v-for="a in latestAchievements"
                            :key="a.type + '-' + a.id + '-' + a.awarded_at"
                            class="flex items-start gap-3 border-b border-gray-200 pb-3 dark:border-gray-700"
                        >
                            <span>🏆</span>
                            <div>
                                <p class="font-semibold text-black dark:text-white">
                                    <template v-if="a.type === 'family'">
                                        🎉 Family unlocked <span class="text-blue-600">{{ a.name }}</span>
                                    </template>
                                    <template v-else>
                                        {{ a.member }} earned <span class="text-blue-600">{{ a.name }}</span>
                                    </template>
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ a.description }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ new Date(a.awarded_at).toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">No achievements yet. Start playing to earn some!</p>
                </div>

                <!-- Challenges & Activity -->
                <div class="rounded-xl bg-white p-6 shadow-md dark:bg-gray-900">
                    <h3 class="mb-4 text-xl font-bold text-black dark:text-white">Challenges and Activity</h3>
                    <template v-if="activeChallenges.length">
                        <div
                            v-for="c in activeChallenges"
                            :key="c.id"
                            class="rounded border p-4"
                            :class="c.completed ? 'bg-green-50 dark:bg-green-900' : 'bg-blue-50 dark:bg-blue-900'"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold">{{ c.challenge.title }}</div>
                                    <div class="text-xs text-gray-500">{{ c.challenge.description }}</div>
                                </div>
                                <div class="text-sm text-gray-600">{{ Math.min((c.progress / c.goal) * 100, 100).toFixed(0) }}%</div>
                            </div>
                            <div class="mt-3 h-3 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                <div
                                    class="h-3 rounded-full"
                                    :class="c.completed ? 'bg-green-600' : 'bg-blue-600'"
                                    :style="{ width: Math.min((c.progress / c.goal) * 100, 100) + '%' }"
                                ></div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                {{ c.progress }} / {{ c.goal }} completed
                                <span v-if="c.completed" class="ml-2 font-medium text-green-600">✅ Completed</span>
                            </div>
                        </div>
                    </template>
                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">No active challenges yet. Check back later once you start playing!</p>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-md dark:bg-gray-900">
                    <div class="mt-6">
                        <h3 class="mb-4 text-xl font-bold text-black dark:text-white">Family Activity (over 3 weeks)</h3>
                        <ActivityChart :data="history" />
                    </div>
                </div>
            </div>
        </AppLayout>
    </div>
</template>
