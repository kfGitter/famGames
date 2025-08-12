<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { defineProps, ref, computed } from 'vue';

const props = defineProps<{
    games: Array<{
        id: number;
        title: string;
        description: string | null;
    }>;
}>();

const search = ref('');

// Computed filtered list of games
const filteredGames = computed(() => {
    return props.games.filter((game) =>
        game.title.toLowerCase().includes(search.value.toLowerCase())
    );
});
</script>


<template>
    <AppLayout>
        <div class="p-6">
            <!-- Search -->
            <div class="mb-4">
                <input v-model="search" type="text" placeholder="Search games..." class="w-full rounded border border-gray-300 p-2 text-black" />
            </div>

            <!-- Scrollable game list -->
            <div class="max-h-[70vh] divide-y overflow-y-auto">
    <!-- <div class="mx-auto max-w-4xl rounded bg-white p-6 text-black shadow"> -->
        
        <!-- <h1 class="mb-6 text-2xl font-bold border-0">My Games</h1> -->
        <div v-if="filteredGames.length === 0" class="text-center text-gray-500">You have not added any games yet.</div>
        <ul>
            <li v-for="game in filteredGames"  :key="game.id" class="mb-4 rounded border border-gray-300 p-4 hover:bg-gray-100">
                <h2 class="text-xl font-semibold">{{ game.title }}</h2>
                <p class="text-gray-700">{{ game.description || 'No description available.' }}</p>
            </li>
        </ul>


    </div>
        </div>
    </AppLayout>
</template>
