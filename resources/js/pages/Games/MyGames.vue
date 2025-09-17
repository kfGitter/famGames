<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { StarIcon as StarOutline } from '@heroicons/vue/24/outline';
import { StarIcon as StarSolid } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';
import { computed, defineProps, ref, watch } from 'vue';

interface Game {
    id: number;
    title: string;
    description: string | null;
    tags: Array<{ id: number; name: string }>;
    custom?: boolean;
    is_favorite?: boolean;
}

const props = defineProps<{ games: Game[] }>();

const search = ref('');

// local copy so we can remove locally
const localGames = ref<Game[]>([...props.games]);
const selectedTags = ref<number[]>([]);
const allTags = ref<Game['tags']>([]);

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
    // Use a direct URL instead of Ziggy
    window.location.href = `/games/${game.id}/${type}`;
}

function startGame(game) {
    const type = game.custom ? 'custom' : 'system';
    window.location.href = `/start-game/${game.id}/${type}`;
}

async function removeGame(game: Game) {
    if (!confirm(`Remove "${game.title}" from your games?`)) return;

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        const type = game.custom ? 'custom' : 'system';
        const res = await fetch(`/my-games/${game.id}?type=${type}`, {
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

import axios from 'axios';

// const toggleFavorite = async (game: any) => {
//     const type = game.custom ? 'custom' : 'system';

//     try {
//         await axios.post(`/my-games/${type}/${game.id}/favorite`);
//         game.is_favorite = !game.is_favorite; // flip star instantly
//     } catch (err) {
//         console.error('Error toggling favorite:', err);
//     }
// };

// const toggleFavorite = (game: any) => {
//     const type = game.is_custom ? 'custom' : 'system';

//     router.post(`/my-games/${type}/${game.id}/favorite`, {}, {
//         preserveScroll: true,
//         onSuccess: (page) => {
//             // backend now returns the updated value
//             if (page.props.is_favorite !== undefined) {
//                 game.is_favorite = page.props.is_favorite;
//             }
//         },
//         onError: (err) => {
//             console.error("Error toggling favorite:", err);
//         }
//     });
// };

// const toggleFavorite = async (game: any) => {
//     const type = game.is_custom ? 'custom' : 'system';

//     try {
//         const response = await axios.post(`/my-games/${type}/${game.id}/favorite`);
//         game.is_favorite = response.data.is_favorite; // update from backend
//     } catch (err) {
//         console.error('Error toggling favorite:', err);
//     }
// };
const toggleFavorite = async (game: Game) => {
    const type = game.custom ? 'custom' : 'system';
    try {
        const { data } = await axios.post(`/my-games/${type}/${game.id}/favorite`);
        game.is_favorite = data.is_favorite; // ✅ updates Vue object directly
    } catch (err) {
        console.error('Error toggling favorite:', err);
    }
};

</script>

<template>
    <AppLayout>
        <div class="p-6">
            <!-- Search -->
            <div class="mb-4">
                <input v-model="search" type="text" placeholder="Search games..." class="w-full rounded border border-gray-300 p-2 text-black" />
            </div>

            <div class="mb-4 flex justify-end">
                <Link href="/my-games/create" class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"> + Add Custom Game </Link>
            </div>

            <!-- Tags  -->
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

            <!-- Scrollable game list -->
            <div class="max-h-[70vh] divide-y overflow-y-auto">
                <div v-if="filteredGames.length === 0" class="text-center text-gray-500">You have not added any games yet.</div>

                <ul>
                    <li v-for="game in filteredGames" :key="game.id" class="mb-4 rounded border border-gray-300 p-4 hover:bg-gray-100">
                        <!-- Star -->
                        <button @click="toggleFavorite(game)">
                            <component
                                :is="game.is_favorite ? StarSolid : StarOutline"
                                class="h-6 w-6"
                                :class="game.is_favorite ? 'text-yellow-400' : 'text-gray-400'"
                            />
                        </button>

                        

                        <!-- game name -->
                        <h2 class="text-xl font-semibold">{{ game.title }}</h2>

                        <!-- buttons -->
                        <div class="flex justify-end gap-2">
                            <button @click="viewGame(game)" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                                View Details
                            </button>
                            <button @click="startGame(game)" class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700">Start</button>
                            <button @click="removeGame(game)" class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700">Remove</button>
                        </div>

                        <p class="text-gray-700">{{ game.description || 'No description available.' }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
