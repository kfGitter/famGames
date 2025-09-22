<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import { route } from 'ziggy-js';

const toast = useToast();

const props = defineProps<{
    gameSession: { id: number; status: string };
    game: { id: number; title: string; description: string; rules?: string; scoring?: string };
    players: { id: number; name: string }[];
}>();

const scores = ref(
    props.players.map((p) => ({
        family_member_id: p.id,
        score: 0,
    })),
);

function saveResults() {
    router.post(
        route('game.session.scores.save', props.gameSession.id),
        { scores: scores.value },
        {
            onSuccess: () => {
                toast.success('Scores saved successfully!', { timeout: 1500 });
                setTimeout(() => router.get('/dashboard'), 1600);
            },
            onError: () => {
                toast.error('Failed to save scores. Try again.');
            },
        },
    );
}
</script>

<template>
    <AppLayout>
        <div class="flex min-h-screen items-center justify-center bg-gray-50 p-6">
            <div class="w-full max-w-md space-y-6 rounded-xl bg-white p-6 shadow-lg">
                <h1 class="text-2xl font-bold text-gray-900">Enter Game Scores</h1>
                <h2 class="text-xl text-gray-700">{{ props.game.title }}</h2>
                <p>{{ props.game.description }}</p>
                <p>{{ props.game.rules }}</p>
                <p>{{ props.game.scoring }}</p>

                <div class="space-y-3">
                    <div v-for="(entry, idx) in scores" :key="entry.family_member_id" class="flex items-center gap-4">
                        <span class="w-32 font-medium text-gray-800">{{ props.players[idx].name }}</span>
                        <input
                            v-model.number="scores[idx].score"
                            type="number"
                            class="w-24 rounded border border-gray-300 bg-gray-50 p-2 text-gray-900 focus:ring-2 focus:ring-green-400 focus:outline-none"
                        />
                    </div>
                </div>

                <button
                    @click="saveResults"
                    class="mt-4 w-full rounded-lg bg-green-600 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-green-700"
                >
                    Save Results
                </button>
            </div>
        </div>
    </AppLayout>
</template>
