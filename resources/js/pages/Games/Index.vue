<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref } from 'vue';
import { StarIcon as StarOutline } from '@heroicons/vue/24/outline';
import { StarIcon as StarSolid } from '@heroicons/vue/24/solid';
import axios from 'axios';

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
        const matchesTags =
            selectedTags.value.length === 0 ||
            selectedTags.value.every((tagId) => game.tags.some((t) => t.id === tagId));
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

async function addToMyGames(game) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    try {
        await fetch('/my-games', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({ game_id: game.id }),
        });
        alert(`${game.title} added to MyGames!`);
    } catch {
        alert(`Failed to add ${game.title}. Try again.`);
    }
}

// Favorite toggle
const toggleFavorite = async (game) => {
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
      <h1 class="text-3xl font-bold">Game Library</h1>
      <!-- Search -->
      <input v-model="search" type="text" placeholder="Search games..." class="w-full rounded border border-gray-300 p-2 text-black" />

      <!-- Tags -->
      <div class="flex gap-2 overflow-x-auto py-2 no-scrollbar">
        <button
          v-for="tag in allTags"
          :key="tag.id"
          @click="toggleTag(tag.id)"
          :class="[
            'rounded-full border px-3 py-1 text-sm transition whitespace-nowrap',
            selectedTags.includes(tag.id)
              ? 'bg-blue-600 border-blue-600 text-white'
              : 'bg-gray-100 border-gray-300 text-gray-700 hover:bg-gray-200',
          ]"
        >
          {{ tag.name }}
        </button>
      </div>

      <!-- Game List -->
      <div class="grid md:grid-cols-2 gap-4 max-h-[70vh] overflow-y-auto">
        <div
          v-for="game in filteredGames"
          :key="game.id"
          class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow hover:shadow-lg transition transform hover:scale-105 relative"
        >
          <!-- Favorite Star -->
          <!-- <button @click="toggleFavorite(game)" class="absolute top-3 right-3">
            <component
              :is="game.is_favorite ? StarSolid : StarOutline"
              class="h-6 w-6"
              :class="game.is_favorite ? 'text-yellow-400' : 'text-gray-400'"
            />
          </button> -->

          <!-- Title -->
          <h2 class="text-xl font-semibold mb-2">{{ game.title }}</h2>

          <!-- Description -->
          <p class="text-gray-700 mb-3">{{ game.description || 'No description available.' }}</p>

          <!-- Actions -->
          <div class="flex justify-end gap-2">
            <button @click="viewGame(game)" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">View Details</button>
            <button @click="addToMyGames(game)" class="rounded bg-green-600 px-3 py-1 text-white hover:bg-green-700">Add to MyGames</button>
          </div>
        </div>
      </div>

      <div v-if="filteredGames.length === 0" class="text-center text-gray-500 mt-4">No games found.</div>
    </div>
  </AppLayout>
</template>
