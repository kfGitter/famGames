<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref } from 'vue';

// Props
const props = defineProps<{
    games: Array<any>;
    tags: Array<{ id: number; name: string }>;
}>();

const search = ref('');
const selectedTags = ref<number[]>([]);
const localGames = ref([...props.games]);
const allTags = ref(props.tags);

// Computed filtered games
const filteredGames = computed(() => {
    return localGames.value.filter((game) => {
        const matchesSearch = game.title.toLowerCase().includes(search.value.toLowerCase());
        const matchesTags = selectedTags.value.length === 0 || selectedTags.value.every((tagId) => game.tags.some((t) => t.id === tagId));
        return matchesSearch && matchesTags;
    });
});

// Tag toggling
function toggleTag(tagId: number) {
    if (selectedTags.value.includes(tagId)) {
        selectedTags.value = selectedTags.value.filter((id) => id !== tagId);
    } else {
        selectedTags.value.push(tagId);
    }
}

// Game actions
function viewGame(game) {
    const type = game.custom ? 'custom' : 'system';
    window.location.href = `/games/${game.id}/${type}`;
}
// Add a game to the user's "MyGames" list
async function addToMyGames(game) {
    // Get CSRF token from meta tag for security
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    try {
        // Send POST request to add the game
        await fetch('/my-games', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({ game_id: game.id }),
        });
        // Notify success
        alert(`${game.title} added to MyGames!`);
    } catch {
        // Notify failure
        alert(`Failed to add ${game.title}. Try again.`);
    }
}
</script>

<template>
    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-3xl font-bold">Game Library</h1>
            <!-- Search -->
            <input v-model="search" type="text" placeholder="Search games..." class="w-full rounded border border-gray-300 p-2 text-black" />

            <!-- Tags -->
            <div class="no-scrollbar flex gap-2 overflow-x-auto py-2">
                <button
                    v-for="tag in allTags"
                    :key="tag.id"
                    @click="toggleTag(tag.id)"
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

            <!-- Game List -->
            <div class="grid max-h-[70vh] gap-4 overflow-y-auto md:grid-cols-2">
                <div
                    v-for="game in filteredGames"
                    :key="game.id"
                    class="relative transform rounded-xl bg-white p-4 shadow transition hover:scale-105 hover:shadow-lg dark:bg-gray-800"
                >
                    <!-- Title -->
                    <h2 class="mb-2 text-xl font-semibold">{{ game.title }}</h2>

                    <!-- Description -->
                    <p class="mb-3 text-gray-700">{{ game.description || 'No description available.' }}</p>

                    <!-- Actions -->
                    <div class="flex justify-end gap-2">
                        <button @click="viewGame(game)" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">View Details</button>
                        <button @click="addToMyGames(game)" class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700">
                            Add to MyGames
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="filteredGames.length === 0" class="mt-4 text-center text-gray-500">No games found.</div>
        </div>
    </AppLayout>
</template>
