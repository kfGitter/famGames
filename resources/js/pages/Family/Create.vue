<script setup>
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    name: '',
    age: '',
    avatar: null,
});

function onFileChange(e) {
    form.avatar = e.target.files[0];
}

function submit() {
    // If you have Ziggy: router.post(route('family-members.store'), form)
    // Otherwise: use the path directly:
    router.post('/family-members', form);
}
</script>

<template>
    <AppLayout>
    <div class="mx-auto max-w-md p-6">
        <h1 class="mb-4 text-xl font-bold">ADD NEW MEMBER</h1>

        <div class="space-y-4">
            <div>
                <label class="mb-1 block font-medium">Member Name</label>
                <input v-model="form.name" class="w-full rounded border p-2" />
            </div>

            <div>
                <label class="mb-1 block font-medium">Age</label>
                <input v-model="form.age" type="number" min="0" class="w-full rounded border p-2" />
            </div>

            <div>
                <label class="mb-1 block font-medium">Upload Avatar (optional)</label>
                <input type="file" @change="onFileChange" accept="image/*" />
            </div>

            <div class="pt-4">
                <button @click="submit" class="rounded bg-blue-600 px-4 py-2 text-white" :disabled="form.processing">Save</button>
            </div>
        </div>
    </div>
    </AppLayout>
</template>
