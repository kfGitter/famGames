<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  title: '',
  description: '',
  rules: '',
  min_players: '',
  max_players: '',
  category: '',
  custom: true, // important: tells backend it’s a custom game
})

function submit() {
  form.post('/my-games', {
    onSuccess: () => {
      form.reset()
    },
  })
}
</script>

<template>
  <div class="mx-auto max-w-xl">
    <h1 class="mb-4 text-2xl font-bold">Add a Custom Game</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block font-medium">Title *</label>
        <input
          v-model="form.title"
          class="w-full rounded border p-2"
          required
        />
        <div v-if="form.errors.title" class="text-sm text-red-500">
          {{ form.errors.title }}
        </div>
      </div>

      <div>
        <label class="block font-medium">Description</label>
        <textarea
          v-model="form.description"
          class="w-full rounded border p-2"
        ></textarea>
      </div>

      <div>
        <label class="block font-medium">Rules</label>
        <textarea
          v-model="form.rules"
          class="w-full rounded border p-2"
        ></textarea>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-medium">Min Players</label>
          <input
            type="number"
            v-model="form.min_players"
            class="w-full rounded border p-2"
          />
        </div>
        <div>
          <label class="block font-medium">Max Players</label>
          <input
            type="number"
            v-model="form.max_players"
            class="w-full rounded border p-2"
          />
        </div>
      </div>

      <div>
        <label class="block font-medium">Category</label>
        <input
          v-model="form.category"
          class="w-full rounded border p-2"
        />
      </div>

      <button
        type="submit"
        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
        :disabled="form.processing"
      >
        Save Game
      </button>

      <Link
        href="/my-games"
        class="ml-2 text-gray-600 hover:underline"
      >
        Cancel
      </Link>
    </form>
  </div>
</template>
