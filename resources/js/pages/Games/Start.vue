<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

// const props = defineProps({
//     game: {
//         type: Object,
//         required: true,
//         default: () => ({ min_player: 2, title: 'Untitled Game' }),
//     },
//     members: {
//         type: Array,
//         default: () => [],
//     },
// });

const props = defineProps<{
    gameSession: { id: number; status: string };
    game: { id: number; title: string; min_players: number };
    members: { id: number; name: string }[];
}>();

const selectedPlayers = ref<number[]>([]);

function startGame() {
    const minPlayers = props.game.min_players;

    if (selectedPlayers.value.length < minPlayers) {
        alert(`⚠️ This game requires at least ${minPlayers} players.`);
        return;
    }

    router.post(`/start-game/${props.game.id}`, {
        players: selectedPlayers.value,
    });
}
</script>

<template>
    <AppLayout>
        <div class="flex min-h-screen items-center justify-center bg-gray-50 p-6">
            <div class="w-full max-w-md space-y-6 rounded-xl bg-white p-6 shadow-lg">
                <h1 class="text-2xl font-bold text-gray-900">Start New Game</h1>

                <!-- Game Info -->
                <div>
                    <label class="mb-1 block font-semibold text-gray-700">Game:</label>
                    <div class="rounded border border-gray-300 bg-gray-100 p-2 text-gray-800">
                        {{ props.game.title }}
                    </div>
                </div>

                <!-- Min Players info -->
                <div>
                    <label class="mb-1 block font-semibold text-gray-700">Minimum Players:</label>
                    <div class="rounded border border-gray-300 bg-gray-100 p-2 text-gray-800">
                        {{ props.game.min_players }}
                    </div>
                </div>

                <!-- Player Selection -->
                <div>
                    <label class="mb-2 block font-semibold text-gray-700">Select Players:</label>
                    <div class="max-h-60 space-y-2 overflow-y-auto">
                        <div v-for="member in members" :key="member.id" class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                :value="member.id"
                                v-model="selectedPlayers"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-gray-800">{{ member.name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Start Button -->
                <button
                    @click="startGame"
                    class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    Start Game
                </button>
            </div>
        </div>
    </AppLayout>
</template>
