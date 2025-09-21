<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

// const props = defineProps<{ members: Array<any> }>();
const props = defineProps({
  members: {
    type: Array,
    required: true
  }
});
</script>

<template>
  <AppLayout>
<div class="p-6 space-y-4">
      <div class="flex items-center justify-between mb-6">
  <h1 class="text-3xl font-bold">Family Members</h1>
  <Link
    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 transition"
    href="/family-members/create"
  >
    + Add Member
  </Link>
</div>


      <div v-if="members.length === 0" class="text-gray-500 text-center p-6 bg-gray-50 rounded-xl">
        No family members yet. Click <span class="font-semibold">“Add Member”</span> to add some and start playing!.
      </div>

      <ul v-else class="space-y-4">
        <li v-for="m in members" :key="m.id" 
            class="flex items-center justify-between rounded-xl bg-white shadow p-4 hover:shadow-md transition">
          <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center">
              <img v-if="m.avatar" :src="`/storage/${m.avatar}`" class="h-full w-full object-cover" />
              <span v-else class="font-bold text-gray-500">{{ m.name[0] }}</span>
            </div>
            <div>
              <div class="font-medium text-lg">{{ m.name }}</div>
              <div v-if="m.age" class="text-sm text-gray-500">{{ m.age }} y/o</div>
            </div>
          </div>
          <Link :href="`/family-members/${m.id}`" class="text-blue-600 hover:underline text-sm">
            View Profile →
          </Link>
        </li>
      </ul>
    </div>
  </AppLayout>
</template>
