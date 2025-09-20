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
        // category: string | null;
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
    <div class="mx-auto max-w-3xl rounded bg-white p-6 text-black shadow">
        <!-- Back Button -->
        <div class="mb-4">
            <Button @click="goBack" class="bg-gray-300 text-black hover:bg-gray-400"> ← Back </Button>
        </div>

        <!-- Title -->
        <h1 class="mb-2 text-3xl font-bold">{{ game.title }}</h1>

        <!-- Category -->
        <!-- <div v-if="game.category" class="mb-4 text-sm text-gray-500">Category: {{ game.category }}</div> -->

        <!-- Description -->
        <div v-if="game.description" class="mb-4">
            <h2 class="mb-1 text-xl font-semibold">Description</h2>
            <p class="text-gray-800">{{ game.description }}</p>
        </div>

        <!-- Rules -->
        <div v-if="game.rules" class="mb-4">
            <h2 class="mb-1 text-xl font-semibold">Rules</h2>
            <p class="whitespace-pre-line text-gray-800">{{ game.rules }}</p>
        </div>

        <!-- Scoring -->
        <div v-if="game.scoring" class="mb-4">
            <h2 class="mb-1 text-xl font-semibold">Scoring</h2>
            <p class="text-gray-800">{{ game.scoring }}</p>
        </div>
        
        <!-- Player Range -->
        <div v-if="game.min_players || game.max_players" class="mb-4">
            <h2 class="mb-1 text-xl font-semibold">Players</h2>
            <p class="text-gray-800">{{ game.min_players ?? '?' }} - {{ game.max_players ?? '?' }} players</p>
        </div>

        <!-- Add to My Games Button -->
        <!-- <div class="mt-6">
            <Button @click="addToMyGames" class="bg-green-600 text-white hover:bg-green-700"> Add to My Games </Button>
        </div> -->
    </div>
</template>
