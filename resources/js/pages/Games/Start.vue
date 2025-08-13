<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    game: Object,
    members: Array,
});

const selectedPlayers = ref([]);

function togglePlayer(id) {
    if (selectedPlayers.value.includes(id)) {
        selectedPlayers.value = selectedPlayers.value.filter((pid) => pid !== id);
    } else {
        selectedPlayers.value.push(id);
    }
}

function startGame() {
    router.post(`/start-game/${props.game.id}`, {
        players: selectedPlayers.value,
    });
}
</script>

<template>
    <AppLayout>
    <div class="p-6">
        <h1 class="mb-4 text-2xl font-bold">START NEW GAME</h1>

        <div class="mb-4">
            <label class="mb-2 block font-semibold">Game:</label>
            <div class="rounded border bg-gray-100 p-2">{{ props.game.title }}</div>
        </div>

        <div class="mb-4">
            <label class="mb-2 block font-semibold">Select Players:</label>
            <div v-for="member in members" :key="member.id" class="mb-1 flex items-center gap-2">
                <input type="checkbox" :value="member.id" v-model="selectedPlayers" />
                <span>{{ member.name }}</span>
            </div>
        </div>

        <button @click="startGame" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Start Game</button>
    </div>
    </AppLayout>
</template>
