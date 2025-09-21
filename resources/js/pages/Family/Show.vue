<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

interface StreakInfo { current: number; best: number }

const props = defineProps<{
  member: { id: number; name: string; avatar?: string | null };
  stats: { totalPlayed: number; wins: number };
  topScores: { game_id: number; game_title: string; best_score: number }[];
  recordsHeld: { game_title: string; record_score: number }[];
  achievements: { id: number; name: string; description?: string|null }[];
  streaks: { daily: StreakInfo; weekly: StreakInfo };
}>();
</script>

<template>
  <AppLayout>
    <div class="space-y-8 p-6">
      <!-- Header -->
      <div class="flex items-center gap-4">
        <div class="h-20 w-20 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center">
          <img v-if="member.avatar" :src="`/storage/${member.avatar}`" class="h-full w-full object-cover" />
          <span v-else class="text-2xl font-bold text-gray-500">{{ member.name[0] }}</span>
        </div>
        <h1 class="text-3xl font-bold">{{ member.name }}’s Profile</h1>
      </div>

      <!-- Streaks -->
      <section class="bg-orange-50 p-4 rounded-xl">
        <h2 class="mb-3 text-xl font-semibold">🔥 Streaks</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
          <div class="rounded-lg bg-white p-4 shadow text-center">
            <div class="text-sm text-gray-500">Daily Streak</div>
            <div class="text-2xl font-bold">{{ streaks.daily.current }} 🔥</div>
            <div class="text-xs text-gray-400">Best: {{ streaks.daily.best }}</div>
          </div>
          <div class="rounded-lg bg-white p-4 shadow text-center">
            <div class="text-sm text-gray-500">Weekly Streak</div>
            <div class="text-2xl font-bold">{{ streaks.weekly.current }} 🔥</div>
            <div class="text-xs text-gray-400">Best: {{ streaks.weekly.best }}</div>
          </div>
        </div>
      </section>

      <!-- Stats -->
      <div class="grid grid-cols-2 gap-4">
        <div class="rounded-xl bg-white p-4 shadow">
          <div class="text-sm text-gray-500">Total Games Played</div>
          <div class="text-2xl font-bold">{{ stats.totalPlayed }}</div>
        </div>
        <div class="rounded-xl bg-white p-4 shadow">
          <div class="text-sm text-gray-500">Games Won</div>
          <div class="text-2xl font-bold">{{ stats.wins }}</div>
        </div>
      </div>

      <!-- Top Scores -->
      <section>
        <h2 class="mb-2 text-xl font-semibold">🏅 Top Scores</h2>
        <ul class="space-y-2">
          <li v-for="t in topScores" :key="t.game_id" 
              class="flex justify-between rounded-lg bg-white p-3 shadow">
            <span>{{ t.game_title }}</span>
            <span class="font-semibold text-indigo-600">{{ t.best_score }} pts</span>
          </li>
        </ul>
      </section>

      <!-- Records -->
      <section>
        <h2 class="mb-2 text-xl font-semibold">🏆 Records Held</h2>
        <div v-if="recordsHeld.length === 0" class="text-gray-500">No records yet.</div>
        <ul v-else class="space-y-2">
          <li v-for="r in recordsHeld" :key="r.game_title" 
              class="flex justify-between rounded-lg bg-white p-3 shadow">
            <span>{{ r.game_title }}</span>
            <span class="font-semibold text-green-600">{{ r.record_score }} pts</span>
          </li>
        </ul>
      </section>

      <!-- Achievements -->
      <section>
        <h2 class="mb-2 text-xl font-semibold">✨Achievements </h2>
        <div v-if="achievements.length === 0" class="text-gray-500">No achievements yet.</div>
        <div class="flex flex-wrap gap-3">
          <div v-for="a in achievements" :key="a.id" 
               class="rounded-xl bg-white p-3 shadow w-40">
            <div class="font-medium">{{ a.name }}</div>
            <div v-if="a.description" class="text-xs text-gray-500">{{ a.description }}</div>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>
