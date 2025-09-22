<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    members: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-3xl font-bold">Family Members</h1>
                <Link class="rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700" href="/family-members/create">
                    + Add Member
                </Link>
            </div>

            <div v-if="members.length === 0" class="rounded-xl bg-gray-50 p-6 text-center text-gray-500">
                No family members yet. Click <span class="font-semibold">“Add Member”</span> to add some and start playing!.
            </div>

            <!-- List of Family Members -->
            <ul v-else class="space-y-4">
                <li
                    v-for="m in members"
                    :key="m.id"
                    class="flex items-center justify-between rounded-xl bg-white p-4 shadow transition hover:shadow-md"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full bg-gray-200">
                            <img v-if="m.avatar" :src="`/storage/${m.avatar}`" class="h-full w-full object-cover" />
                            <span v-else class="font-bold text-gray-500">{{ m.name[0] }}</span>
                        </div>
                        <div>
                            <div class="text-lg font-medium">{{ m.name }}</div>
                            <!-- <div v-if="m.age" class="text-sm text-gray-500">{{ m.age }} y/o</div> -->
                        </div>
                    </div>
                    <!-- shortcut -->
                    <Link :href="`/family-members/${m.id}`" class="text-sm text-blue-600 hover:underline"> View Profile → </Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
