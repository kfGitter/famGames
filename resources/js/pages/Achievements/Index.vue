<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    members: { id: number; name: string; avatar?: string | null }[];
}>();

function goToMember(memberId: number) {
    router.get(`/achievements/${memberId}`);
}
</script>

<template>
    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-bold">Family Members</h1>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                <div
                    v-for="m in props.members"
                    :key="m.id"
                    class="flex cursor-pointer items-center gap-2 rounded border p-4 hover:bg-gray-100"
                    @click="goToMember(m.id)"
                >
                    <div class="h-12 w-12 overflow-hidden rounded-full bg-gray-200">
                        <img v-if="m.avatar" :src="`/storage/${m.avatar}`" class="h-full w-full object-cover" />
                    </div>
                    <div class="font-medium">{{ m.name }}</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
