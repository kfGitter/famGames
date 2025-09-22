<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';

const form = useForm({
    title: '',
    description: '',
    rules: '',
    min_players: '',
    max_players: '',
    scoring: '',
    custom: true,
    tags: [],
});

const props = defineProps(['tags']);
const allTags = ref(props.tags || []);

const toast = useToast();

function submit() {
    form.post('/my-games', {
        onSuccess: () => {
            toast.success('Custom game created! Redirecting you to My Games...');
            setTimeout(() => {
                window.location.href = `/my-games`;
            }, 1600);
        },
        onError: () => {
            toast.error('Oops! There was a problem creating your game.');
        },
    });
}
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 rounded-xl bg-white p-6 shadow">
            <h1 class="text-2xl font-bold text-gray-900">Add a Custom Game</h1>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Title -->
                <div>
                    <label class="block font-medium text-gray-700">Title *</label>
                    <input
                        v-model="form.title"
                        class="w-full rounded border border-gray-300 p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        required
                    />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block font-medium text-gray-700">Description</label>
                    <textarea
                        v-model="form.description"
                        class="w-full rounded border border-gray-300 p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    ></textarea>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block font-medium text-gray-700">Tags</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <label v-for="tag in allTags" :key="tag.id" class="flex cursor-pointer items-center gap-1">
                            <input
                                type="checkbox"
                                :value="tag.id"
                                v-model="form.tags"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-gray-800">{{ tag.name }}</span>
                        </label>
                    </div>
                </div>

                <!-- Rules -->
                <div>
                    <label class="block font-medium text-gray-700">Rules</label>
                    <textarea
                        v-model="form.rules"
                        class="w-full rounded border border-gray-300 p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    ></textarea>
                </div>

                <!-- Scoring -->
                <div>
                    <label class="block font-medium text-gray-700">Scoring</label>
                    <input
                        v-model="form.scoring"
                        class="w-full rounded border border-gray-300 p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    />
                </div>

                <!-- Min / Max Players -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-gray-700">Min Players</label>
                        <input
                            type="number"
                            v-model="form.min_players"
                            class="w-full rounded border border-gray-300 p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700">Max Players</label>
                        <input
                            type="number"
                            v-model="form.max_players"
                            class="w-full rounded border border-gray-300 p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Last actions -->
                <div class="flex items-center gap-4">
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-blue-700"
                        :disabled="form.processing"
                    >
                        Save Game
                    </button>
                    <Link href="/my-games" class="text-gray-600 hover:underline">Cancel</Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
