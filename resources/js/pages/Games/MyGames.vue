<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, defineProps, ref, watch } from 'vue';

interface Game {
    id: number;
    title: string;
    description: string | null;
}

const props = defineProps<{ games: Game[] }>();

const search = ref('');

// local copy so we can remove locally
const localGames = ref<Game[]>([...props.games]);

watch(
    () => props.games,
    (val) => {
        localGames.value = [...val];
    },
    { deep: true },
);

const filteredGames = computed(() => {
    return localGames.value.filter((game) => game.title.toLowerCase().includes(search.value.toLowerCase()));
});

async function removeGame(game: Game) {
    if (!confirm(`Remove "${game.title}" from your games?`)) return;

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        const res = await fetch(`/my-games/${game.id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                Accept: 'application/json',
            },
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({ message: 'Failed to remove game' }));
            throw new Error(err.message || 'Failed to remove game');
        }

        // Remove from local list
        localGames.value = localGames.value.filter((g) => g.id !== game.id);
    } catch (error: any) {
        console.error(error);
        alert(error.message || 'Could not remove game. Try again.');
    }
}
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
                <div v-if="filteredGames.length === 0" class="text-center text-gray-500">You have not added any games yet.</div>

                <ul>
                    <li v-for="game in filteredGames" :key="game.id" class="mb-4 rounded border border-gray-300 p-4 hover:bg-gray-100">
                        <h2 class="text-xl font-semibold">{{ game.title }}</h2>

                        <div class="flex justify-end gap-2">
                            <!-- View details using Inertia Link -->
                            <Link :href="`/games/${game.id}`" class="rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700">
                                View details
                            </Link>

                            <!-- Start using Inertia Link to /start-game/:id -->
                            <Link :href="`/start-game/${game.id}`" class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700">
                                Start
                            </Link>

                            <button @click="removeGame(game)" class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">Remove</button>
                        </div>

                        <p class="text-gray-700">{{ game.description || 'No description available.' }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
