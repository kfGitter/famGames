<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

// Define form fields inline to satisfy useForm's requirements
const form = useForm({
    name: '',
    age: null as number | null,
    avatar: null as File | null,
});

function onFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.avatar = target.files[0];
    }
}

function submit() {
    form.post('/family-members', {
        onSuccess: () => {
            // SPA-friendly redirect using Inertia
            window.location.href = '/family-members';
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
        },
    });
}
</script>

<template>
  <AppLayout>
    <div class="mx-auto max-w-md p-6 bg-white rounded-xl shadow-lg border border-gray-200">
      <h1 class="mb-6 text-2xl font-bold text-center text-gray-800">Add New Family Member</h1>

      <div class="space-y-5">
        <!-- Name -->
        <div>
          <label class="block mb-1 font-medium text-gray-700">Member Name / Code Name</label>
          <input 
            v-model="form.name"
            class="w-full rounded-lg border border-gray-300 p-3 focus:ring-2 focus:ring-blue-400 focus:outline-none"
            placeholder="Enter full name"
          />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
        </div>

        <!-- Age -->
        <div>
          <label class="block mb-1 font-medium text-gray-700">Age</label>
          <input 
            v-model="form.age"
            type="number"
            min="0"
            class="w-full rounded-lg border border-gray-300 p-3 focus:ring-2 focus:ring-blue-400 focus:outline-none"
            placeholder="Optional"
          />
          <p v-if="form.errors.age" class="mt-1 text-sm text-red-500">{{ form.errors.age }}</p>
        </div>

        <!-- Avatar -->
        <div>
          <label class="block mb-1 font-medium text-gray-700">Upload Avatar (optional)</label>
          <input 
            type="file" 
            @change="onFileChange" 
            accept="image/*"
            class="w-full rounded-lg border border-gray-300 p-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
          />
          <p v-if="form.errors.avatar" class="mt-1 text-sm text-red-500">{{ form.errors.avatar }}</p>
        </div>

        <!-- Submit -->
        <div class="pt-4 flex justify-end">
          <button 
            @click="submit" 
            class="rounded-lg bg-blue-600 px-6 py-2 text-white font-medium hover:bg-blue-700 active:scale-95 transition"
            :disabled="form.processing"
          >
            Save
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
