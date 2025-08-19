<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    members: Array,
});
</script>

<template>
    <AppLayout>
    <div class="mx-auto max-w-3xl p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold">FAMILY MEMBERS</h1>
            <Link class="btn" href="/family-members/create">Add New Member</Link>
        </div>

        <div v-if="members.length === 0" class="text-gray-500">No family members yet. Click "Add New Member" to create one.</div>

        <ul v-else class="space-y-4">
            <li v-for="m in members" :key="m.id" class="flex items-center justify-between rounded border p-3">
                <div class="flex items-center gap-4">
                    <img v-if="m.avatar" :src="`/storage/${m.avatar}`" class="h-12 w-12 rounded-full object-cover" />
                    <div>
                        <div class="font-medium">{{ m.name }}</div>
                        <div class="text-sm text-gray-500" v-if="m.age">{{ m.age }} y/o</div>
                    </div>
                </div>
                <div>
                    <Link :href="`/family-members/${m.id}`" class="text-sm text-blue-600">View Profile</Link>
                </div>
            </li>
        </ul>
    </div>
    </AppLayout>
</template>
