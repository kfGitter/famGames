<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    game: {
        id: number;
        title: string;
        description: string | null;
        rules: string | null;
        min_players: number | null;
        max_players: number | null;
        scoring: string | null;
    };
}>();

const page = usePage();

const addToMyGames = () => {
    router.post(route('my-games.store', props.game.id));
};
const goBack = () => {
    window.history.back();
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 p-6">
        <div class="w-full max-w-md space-y-6 rounded-xl bg-white p-6 shadow-lg">
           
          <!-- Back Button -->
            <div class="mb-4 text-left">
                <Button @click="goBack" class="bg-gray-300 text-black shadow-sm hover:bg-gray-400"> ← Back </Button>
            </div>

            <!-- Title -->
            <h1 class="mb-4 text-3xl font-bold text-gray-900">{{ game.title }}</h1>

            <!-- Description -->
            <div v-if="game.description" class="mb-6">
                <h2 class="mb-2 text-xl font-semibold text-gray-800">Description</h2>
                <p class="leading-relaxed text-gray-700">{{ game.description }}</p>
            </div>

            <!-- Rules -->
            <div v-if="game.rules" class="mb-6">
                <h2 class="mb-2 text-xl font-semibold text-gray-800">Rules</h2>
                <p class="leading-relaxed whitespace-pre-line text-gray-700">{{ game.rules }}</p>
            </div>

            <!-- Scoring -->
            <div v-if="game.scoring" class="mb-6">
                <h2 class="mb-2 text-xl font-semibold text-gray-800">Scoring</h2>
                <p class="leading-relaxed text-gray-700">{{ game.scoring }}</p>
            </div>

            <!-- Player Range -->
            <div v-if="game.min_players || game.max_players" class="mb-6">
                <h2 class="mb-2 text-xl font-semibold text-gray-800">Players</h2>
                <p class="text-gray-700">{{ game.min_players ?? '?' }} - {{ game.max_players ?? '?' }} players</p>
            </div>

            <!-- Add to My Games Button -->
            <!-- Uncomment if needed -->
            <!--
    <div class="mt-6">
      <Button @click="addToMyGames" class="bg-green-600 text-white hover:bg-green-700 shadow-sm"> Add to My Games </Button>
    </div>
    -->
        </div>
    </div>
</template>
