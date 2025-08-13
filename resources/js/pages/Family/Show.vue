<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    member: Object,
});
</script>

<template>
    <div class="mx-auto max-w-md p-6">
        <h1 class="mb-4 text-2xl font-bold">{{ member.name }}</h1>

        <div class="mb-4">
            <img v-if="member.avatar" :src="`/storage/${member.avatar}`" class="h-32 w-32 rounded-full object-cover" />
        </div>

        <div class="mb-2">
            <strong>Age:</strong> <span>{{ member.age ?? '—' }}</span>
        </div>

        <div class="mt-6">
            <Link href="/family-members" class="text-sm text-blue-600">Back to list</Link>
        </div>
    </div>
</template>
