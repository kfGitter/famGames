<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
  mostPlays: { family_member_id: number; plays: number; member: { id: number; name: string; avatar?: string|null } }[];
  mostWins: { family_member_id: number; wins: number; member: { id: number; name: string; avatar?: string|null } }[];
  personalBests: { family_member_id: number; best_score: number; member: { id: number; name: string; avatar?: string|null } }[];
  gameRecords: { game_id: number; title: string; record: number }[];
}>();
</script>

<template>
  <AppLayout>
    <div class="p-6 space-y-8 text-black">
      <h1 class="text-2xl font-bold">Family Leaderboards</h1>

      <section>
        <h2 class="text-xl font-semibold mb-3">Most Plays</h2>
        <div class="grid gap-2">
          <div v-for="r in props.mostPlays" :key="r.family_member_id" class="flex justify-between border p-2 rounded">
            <span>{{ r.member?.name }}</span>
            <span>{{ r.plays }} plays</span>
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-xl font-semibold mb-3">Most Wins</h2>
        <div class="grid gap-2">
          <div v-for="r in props.mostWins" :key="r.family_member_id" class="flex justify-between border p-2 rounded">
            <span>{{ r.member?.name }}</span>
            <span>{{ r.wins }} wins</span>
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-xl font-semibold mb-3">Personal Bests</h2>
        <div class="grid gap-2">
          <div v-for="r in props.personalBests" :key="r.family_member_id" class="flex justify-between border p-2 rounded">
            <span>{{ r.member?.name }}</span>
            <span>{{ r.best_score }} pts</span>
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-xl font-semibold mb-3">Game Records (Family)</h2>
        <div class="grid gap-2">
          <div v-for="g in props.gameRecords" :key="g.game_id" class="flex justify-between border p-2 rounded">
            <span>{{ g.title }}</span>
            <span>{{ g.record }} pts</span>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>
