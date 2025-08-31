<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, defineProps, ref } from 'vue';

const form = useForm({});

interface Game {
    id: number;
    title: string;
    description: string;
}

function viewGame(game) {
    const type = game.custom ? 'custom' : 'system';
    // Use a direct URL instead of Ziggy
    window.location.href = `/games/${game.id}/${type}`;
}

async function addToMyGames(game: Game) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    await fetch('/my-games', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify({ game_id: game.id }),
    });

    alert(`Added ${game.title} to your MyGames!`);
}

// Define props at top level
// const props = defineProps<{
//     games: Array<{
//         id: number;
//         title: string;
//         description: string;
//     }>;
// }>();

const props = defineProps<{
    games: Array<any>;
    tags: Array<{ id: number; name: string }>;
}>();

const search = ref('');
const allTags = ref(props.tags); // all available tags
const localGames = ref([...props.games]); // local copy
const selectedTags = ref<number[]>([]); // currently selected tag IDs

const filteredGames = computed(() => {
    return localGames.value.filter((game) => {
        const matchesSearch = game.title.toLowerCase().includes(search.value.toLowerCase());
        const matchesTags = selectedTags.value.length === 0 || selectedTags.value.every((tagId) => game.tags.some((t) => t.id === tagId));
        return matchesSearch && matchesTags;
    });
});

function toggleTag(tagId: number) {
    if (selectedTags.value.includes(tagId)) {
        // remove if already selected
        selectedTags.value = selectedTags.value.filter((id) => id !== tagId);
    } else {
        // add if not selected
        selectedTags.value.push(tagId);
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

            <!-- <div class="mb-4 flex gap-2">
                <div v-for="tag in allTags" :key="tag.id">
                    <input type="checkbox" :value="tag.id" v-model="selectedTags" />
                    <span>{{ tag.name }}</span>
                </div>
            </div> -->

            <!-- Tag Filter Pills -->
            <div class="no-scrollbar mb-4 flex gap-2 overflow-x-auto py-2">
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

            <!-- Scrollable game list -->
            <div class="max-h-[70vh] divide-y overflow-y-auto rounded border border-gray-200 bg-white">
                <div v-for="game in filteredGames" :key="game.id" class="flex items-center justify-between p-4 text-black hover:bg-gray-100">
                    <!-- Left: Game Title -->
                    <span class="text-lg font-medium">{{ game.title }}</span>

                    <!-- Right: Buttons -->
                    <div class="flex gap-2">
                        <!-- <inertia-link :href="route('games.show', game.id)" class="rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700">
                            View Game
                        </inertia-link> -->
                        <button @click="viewGame(game)" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">View Details</button>

                        <!-- <button type="button" class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700" @click="addToMyGames(game.id)">
                            Add to My Games
                        </button> -->

                        <button @click="addToMyGames(game)" class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700">
                            Add to MyGames
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
