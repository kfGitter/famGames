<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from 'vue-toastification';

const form = useForm({
    title: '',
    description: '',
    rules: '',
    min_players: '',
    max_players: '',
    // category: '',
    scoring: '', // New field for scoring
    custom: true, // important: tells backend it’s a custom game
    tags: [], // New field for tags
});

// Reactive variable for all available tags from backend

const props = defineProps(['tags']);
const allTags = ref([]);
allTags.value = props.tags;


const toast = useToast();

function submit() {
    form.post('/my-games', {
        onSuccess: () => {
            toast.success('Custom game created! Redirecting to My Games...');
            setTimeout(() => {
            window.location.href = `/my-games`;
            }, 1600);
        },
        onError: () => {
            toast.error('Oops! There was a problem creating your game.');
        }
    });
}

</script>

<template>
    <AppLayout>
    <div class="mx-auto max-w-xl">
        <h1 class="mb-4 text-2xl font-bold">Add a Custom Game</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block font-medium">Title *</label>
                <input v-model="form.title" class="w-full rounded border p-2" required />
                <div v-if="form.errors.title" class="text-sm text-red-500">
                    {{ form.errors.title }}
                </div>
            </div>

            <div>
                <label class="block font-medium">Description</label>
                <textarea v-model="form.description" class="w-full rounded border p-2"></textarea>
            </div>

            <div>
                <label class="block font-medium">Tags</label>
                <div class="mt-1 flex flex-wrap gap-2">
                    <div v-for="tag in allTags" :key="tag.id" class="flex items-center gap-1">
                        <input type="checkbox" :value="tag.id" v-model="form.tags" />
                        <span>{{ tag.name }}</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-medium">Rules</label>
                <textarea v-model="form.rules" class="w-full rounded border p-2"></textarea>
            </div>

            
            <div>
                <label class="block font-medium">Scoring</label>
                <input v-model="form.scoring" class="w-full rounded border p-2" />
            </div>


            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Min Players</label>
                    <input type="number" v-model="form.min_players" class="w-full rounded border p-2" />
                </div>
                <div>
                    <label class="block font-medium">Max Players</label>
                    <input type="number" v-model="form.max_players" class="w-full rounded border p-2" />
                </div>
            </div>


            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" :disabled="form.processing">Save Game</button>

            <Link href="/my-games" class="ml-2 text-gray-600 hover:underline"> Cancel </Link>
        </form>
    </div>
    </AppLayout>
</template>
