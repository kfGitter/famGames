<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { StarIcon as StarOutline } from '@heroicons/vue/24/outline';
import { StarIcon as StarSolid } from '@heroicons/vue/24/solid';
import { Link } from '@inertiajs/vue3';
import { computed, defineProps, ref, watch } from 'vue';
import axios from 'axios';

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

// local copy for removal
const localGames = ref<Game[]>([...props.games]);
const selectedTags = ref<number[]>([]);
const allTags = ref<Game['tags']>([]);
const members = computed(() => props.members ?? []);

allTags.value = Array.from(new Map(localGames.value.flatMap((g) => g.tags).map((t) => [t.id, t])).values());

watch(
    () => props.games,
    (val) => { localGames.value = [...val]; },
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
        alert("⚠️ You need at least two family members to start a game.");
        return;
    }
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
    <div class="p-6 space-y-4">
        <h1 class="text-3xl font-bold">My Games</h1>
        <!-- Search -->
        <input
            v-model="search"
            type="text"
            placeholder="Search games..."
            class="w-full rounded border border-gray-300 p-2 text-black focus:ring-2 focus:ring-blue-400 focus:outline-none"
        />

        <!-- Add custom game -->
        <div class="flex justify-end mb-4">
            <Link href="/my-games/create" class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700 shadow-sm">
                + Add Custom Game
            </Link>
        </div>

        <!-- Tags -->
        <div class="flex gap-2 overflow-x-auto py-2 no-scrollbar mb-4">
            <button
                v-for="tag in allTags"
                :key="tag.id"
                @click="() => { if(selectedTags.includes(tag.id)) { selectedTags = selectedTags.filter(id => id !== tag.id) } else { selectedTags.push(tag.id) }}"
                :class="[
                    'rounded-full border px-3 py-1 text-sm whitespace-nowrap transition',
                    selectedTags.includes(tag.id)
                        ? 'bg-blue-600 border-blue-600 text-white'
                        : 'bg-gray-100 border-gray-300 text-gray-700 hover:bg-gray-200',
                ]"
            >
                {{ tag.name }}
            </button>
        </div>

        <!-- Game list -->
        <div class="max-h-[70vh] overflow-y-auto divide-y">
            <div v-if="filteredGames.length === 0" class="text-center text-gray-500 py-10">You have not added any games yet.</div>

            <ul class="space-y-4">
                <li v-for="game in filteredGames" :key="game.id" class="rounded-xl border border-gray-300 p-4 hover:bg-gray-50 shadow-sm transition">
                    <!-- Favorite star -->
                    <button @click="toggleFavorite(game)" class="float-right">
                        <component
                            :is="game.is_favorite ? StarSolid : StarOutline"
                            class="h-6 w-6"
                            :class="game.is_favorite ? 'text-yellow-400' : 'text-gray-400'"
                        />
                    </button>

                    <!-- Game title -->
                    <h2 class="text-xl font-semibold mb-2 text-gray-900">{{ game.title }}</h2>

                    <!-- Buttons -->
                    <div class="flex flex-wrap justify-end gap-2 mb-2">
                        <button @click="viewGame(game)" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 shadow-sm">
                            View Details
                        </button>
                        <button @click="startGame(game)" class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700 shadow-sm">
                            Start
                        </button>
                        <button @click="removeGame(game)" class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700 shadow-sm">
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
