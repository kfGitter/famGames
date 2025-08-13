<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';


const props = defineProps<{
  gameSession: { id: number; status: string };
  game: { id: number; title: string };
  players: { id: number; name: string }[];
}>();

const scores = ref(props.players.map(p => ({
  family_member_id: p.id,
  score: 0
})));


function saveResults() {
  // POST to the exact URL defined in routes/web.php
  router.post(route('game.session.scores.save', props.gameSession.id), {
    scores: scores.value
  });
}
</script>

<template>
  <AppLayout>
    <div class="p-6 text-black">
      <h1 class="text-2xl font-bold mb-4">Enter Game Scores</h1>
      <h2 class="text-xl mb-6">{{ props.game.title }}</h2>

      <div v-for="(entry, idx) in scores" :key="entry.family_member_id" class="mb-4 flex items-center gap-4">
        <span class="w-32">{{ props.players[idx].name }}</span>
        <input v-model.number="scores[idx].score" type="number" class="w-24 border p-1 rounded" />
      </div>

      <button @click="saveResults" class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Save Results
      </button>
    </div>
  </AppLayout>
</template>
