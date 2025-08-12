<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link as InertiaLink, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const form = useForm({});

function addToMyGames(gameId: number) {
    form.post(route('my-games.store', gameId), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Game added to My Games!');
        },
    });
}

// Define props at top level
const props = defineProps<{
    games: Array<{
        id: number;
        title: string;
        description: string;
    }>;
}>();

const search = ref('');

// Computed filtered list
const filteredGames = computed(() => props.games.filter((game) => game.title.toLowerCase().includes(search.value.toLowerCase())));
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <!-- Search -->
            <div class="mb-4">
                <input v-model="search" type="text" placeholder="Search games..." class="w-full rounded border border-gray-300 p-2 text-black" />
            </div>

            <!-- Scrollable game list -->
            <div class="max-h-[70vh] divide-y overflow-y-auto rounded border border-gray-200 bg-white">
                <div v-for="game in filteredGames" :key="game.id" class="flex items-center justify-between p-4 text-black hover:bg-gray-100">
                    <!-- Left: Game Title -->
                    <span class="text-lg font-medium">{{ game.title }}</span>

                    <!-- Right: Buttons -->
                    <div class="flex gap-2">
                        <inertia-link :href="route('games.show', game.id)" class="rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700">
                            View Game
                        </inertia-link>

                        <button type="button" class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700" @click="addToMyGames(game.id)">
                            Add to My Games
                        </button>
                    </div>
                </div>
            </div>

            <!-- No results message -->
            <div v-if="filteredGames.length === 0" class="text-center text-gray-500">No games found.</div>
            <!-- End of scrollable game list -->
        </div>
    </AppLayout>
</template>
