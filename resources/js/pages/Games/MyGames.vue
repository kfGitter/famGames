<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { StarIcon as StarOutline } from '@heroicons/vue/24/outline';
import { StarIcon as StarSolid } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, defineProps, ref, watch } from 'vue';

interface Game {
    id: number;
    title: string;
    description: string | null;
    tags: Array<{ id: number; name: string }>;
    custom?: boolean;
    is_favorite?: boolean;
}

const props = defineProps<{ games: Game[]; members: any[] }>();

const search = ref('');

const localGames = ref<Game[]>([...props.games]);
const selectedTags = ref<number[]>([]);
const allTags = ref<Game['tags']>([]);
const members = computed(() => props.members ?? []);

allTags.value = Array.from(new Map(localGames.value.flatMap((g) => g.tags).map((t) => [t.id, t])).values());

watch(
    () => props.games,
    (val) => {
        localGames.value = [...val];
    },
    { deep: true },
);

const filteredGames = computed(() => {
    return localGames.value.filter((game) => {
        const matchesSearch = game.title.toLowerCase().includes(search.value.toLowerCase());
        const matchesTags = selectedTags.value.length === 0 || selectedTags.value.every((tagId) => game.tags.some((t) => t.id === tagId));
        return matchesSearch && matchesTags;
    });
});

function viewGame(game) {
    const type = game.custom ? 'custom' : 'system';
    window.location.href = `/games/${game.id}/${type}`;
}

function startGame(game) {
    if (!members.value || members.value.length < 2) {
        alert('⚠️ You need at least two family members to start a game.');
        return;
    }
    const type = game.custom ? 'custom' : 'system';
    window.location.href = `/start-game/${game.id}/${type}`;
}

/**
 * Removes a game from the user's games list after confirmation.
 *
 * Prompts the user for confirmation before proceeding. Sends a DELETE request to the server
 * to remove the specified game, using the game's ID and type (custom or system).
 * Updates the local games list on success. Handles and displays errors if the operation fails.
 *
 * @param {Game} game - The game object to be removed.
 */
async function removeGame(game: Game) {
    if (!confirm(`Remove "${game.title}" from your games?`)) return;
    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        const type = game.custom ? 'custom' : 'system';
        const res = await fetch(`/my-games/${game.id}?type=${type}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({ message: 'Failed to remove game' }));
            throw new Error(err.message || 'Failed to remove game');
        }
        localGames.value = localGames.value.filter((g) => g.id !== game.id);
    } catch (error: any) {
        console.error(error);
        alert(error.message || 'Could not remove game. Try again.');
    }
}

/**
 * Toggles the favorite status of a game.
 * Sends a POST request to update the favorite status on the server,
 * then updates the local game object's is_favorite property.
 *
 * @param {Game} game - The game object to toggle favorite status for.
 */
const toggleFavorite = async (game: Game) => {
    const type = game.custom ? 'custom' : 'system';
    try {
        const { data } = await axios.post(`/my-games/${type}/${game.id}/favorite`);
        game.is_favorite = data.is_favorite;
    } catch (err) {
        console.error(err);
    }
};
</script>

<template>
    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-3xl font-bold">My Games</h1>
            <!-- Search -->
            <input
                v-model="search"
                type="text"
                placeholder="Search games..."
                class="w-full rounded border border-gray-300 p-2 text-black focus:ring-2 focus:ring-blue-400 focus:outline-none"
            />

            <!-- Add custom game -->
            <div class="mb-4 flex justify-end">
                <Link href="/my-games/create" class="rounded-lg bg-green-600 px-4 py-2 text-white shadow-sm hover:bg-green-700">
                    + Add Custom Game
                </Link>
            </div>

            <!-- Tags -->
            <div class="no-scrollbar mb-4 flex gap-2 overflow-x-auto py-2">
                <button
                    v-for="tag in allTags"
                    :key="tag.id"
                    @click="
                        () => {
                            if (selectedTags.includes(tag.id)) {
                                selectedTags = selectedTags.filter((id) => id !== tag.id);
                            } else {
                                selectedTags.push(tag.id);
                            }
                        }
                    "
                    :class="[
                        'rounded-full border px-3 py-1 text-sm whitespace-nowrap transition',
                        selectedTags.includes(tag.id)
                            ? 'border-blue-600 bg-blue-600 text-white'
                            : 'border-gray-300 bg-gray-100 text-gray-700 hover:bg-gray-200',
                    ]"
                >
                    {{ tag.name }}
                </button>
            </div>

            <!-- Game list -->
            <div class="max-h-[70vh] divide-y overflow-y-auto">
                <div v-if="filteredGames.length === 0" class="py-10 text-center text-gray-500">You have not added any games yet.</div>

                <ul class="space-y-4">
                    <li
                        v-for="game in filteredGames"
                        :key="game.id"
                        class="rounded-xl border border-gray-300 p-4 shadow-sm transition hover:bg-gray-50"
                    >
                        <!-- Favorite star -->
                        <button @click="toggleFavorite(game)" class="float-right">
                            <component
                                :is="game.is_favorite ? StarSolid : StarOutline"
                                class="h-6 w-6"
                                :class="game.is_favorite ? 'text-yellow-400' : 'text-gray-400'"
                            />
                        </button>

                        <!-- Game title -->
                        <h2 class="mb-2 text-xl font-semibold text-gray-900">{{ game.title }}</h2>

                        <!-- Buttons -->
                        <div class="mb-2 flex flex-wrap justify-end gap-2">
                            <button @click="viewGame(game)" class="rounded-lg bg-blue-600 px-4 py-2 text-white shadow-sm hover:bg-blue-700">
                                View Details
                            </button>
                            <button @click="startGame(game)" class="rounded bg-green-600 px-3 py-1 text-white shadow-sm hover:bg-green-700">
                                Start
                            </button>
                            <button @click="removeGame(game)" class="rounded bg-red-600 px-3 py-1 text-white shadow-sm hover:bg-red-700">
                                Remove
                            </button>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-700">{{ game.description || 'No description available.' }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
