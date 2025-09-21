<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps<{
  gameSession: { id: number; status: string };
  game: { id: number; title: string };
  players: { id: number; name: string }[];
}>();

const scores = ref(
  props.players.map((p) => ({
    family_member_id: p.id,
    score: 0,
  }))
);

function saveResults() {
  router.post(
    route('game.session.scores.save', props.gameSession.id),
    { scores: scores.value },
    {
      onSuccess: () => {
        toast.success('✅ Scores saved successfully!', { timeout: 1500 });
        setTimeout(() => router.get('/dashboard'), 1600);
      },
      onError: () => {
        toast.error('❌ Failed to save scores. Try again.');
      },
    }
  );
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 p-6">
      <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-6 space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">Enter Game Scores</h1>
        <h2 class="text-xl text-gray-700">{{ props.game.title }}</h2>

      <div class="space-y-3">
        <div
          v-for="(entry, idx) in scores"
          :key="entry.family_member_id"
          class="flex items-center gap-4"
        >
          <span class="w-32 font-medium text-gray-800">{{ props.players[idx].name }}</span>
          <input
            v-model.number="scores[idx].score"
            type="number"
              class="w-24 rounded border border-gray-300 p-2 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-green-400 focus:outline-none"
          />
        </div>
      </div>

      <button
        @click="saveResults"
        class="w-full mt-4 bg-green-600 px-4 py-2 rounded-lg text-white font-semibold hover:bg-green-700 shadow-sm transition"
      >
        Save Results
      </button>
    </div>
    </div>
  </AppLayout>
</template>
