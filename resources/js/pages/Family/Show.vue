<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
// import { usePage } from '@inertiajs/vue3';
// import { computed } from 'vue';
import { defineProps } from 'vue';


interface StreakInfo {
  current: number;
  best: number;
}

const props = defineProps<{
    member: { id: number; name: string; avatar?: string | null };
    stats: { totalPlayed: number; wins: number };
    topScores: { game_id: number; game_title: string; best_score: number }[];
    recordsHeld: { game_title: string; record_score: number }[];
    achievements: { id: number; code: string; name: string; icon?: string | null; description?: string | null; awarded_at: string }[];

     family: Record<string, any>,
  streaks: {
    daily: StreakInfo,
    weekly: StreakInfo,
  }
}>();

// type Streak = { count: number; best: number };
// type Streaks = { daily: Streak; weekly: Streak };

// interface PageProps {
//   streaks?: {
//     daily?: Streak;
//     weekly?: Streak;
//   };


  // Add other props if needed


// const page = usePage<PageProps>();

// const streaks = computed<Streaks>(() => ({
//   daily: page.props.streaks?.daily ?? { count: 0, best: 0 },
//   weekly: page.props.streaks?.weekly ?? { count: 0, best: 0 },
// }));
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

            <section>
  <h2 class="mb-2 text-xl font-semibold">Streaks 🔥</h2>
  <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
    <!-- Daily Streak Card -->
    <div class="rounded-lg border p-4 text-center">
      <div class="text-sm text-gray-500">Daily Streak</div>
      <div class="text-2xl font-bold">{{ props.streaks.daily.current }} 🔥</div>
      <div class="text-xs text-gray-400">Best: {{ props.streaks.daily.best }}</div>
    </div>

    <!-- Weekly Streak Card -->
    <div class="rounded-lg border p-4 text-center">
      <div class="text-sm text-gray-500">Weekly Streak</div>
      <div class="text-2xl font-bold">{{ props.streaks.weekly.current }} 🔥</div>
      <div class="text-xs text-gray-400">Best: {{ props.streaks.weekly.best }}</div>
    </div>
  </div>
</section>


            <!-- Rest of the template stays the same -->
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
