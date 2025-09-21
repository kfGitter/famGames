<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
  <AuthLayout title="Forgot password" description="Enter your email to receive a password reset link">
    <Head title="Forgot password" />

    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Email input -->
      <div class="grid gap-2">
        <Label for="email" class="text-gray-600 font-medium">Email address</Label>
        <Input
          id="email"
          type="email"
          name="email"
          autocomplete="off"
          v-model="form.email"
          autofocus
          placeholder="email@example.com"
          class="!bg-gray-50 text-black focus:ring-blue-400 focus:border-blue-400"
        />
        <InputError :message="form.errors.email" />
      </div>

      <!-- Submit button -->
      <div class="flex items-center justify-start">
        <Button class="w-full bg-blue-200 text-blue-900 hover:bg-blue-300 flex items-center justify-center gap-2" :disabled="form.processing">
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
          Email password reset link
        </Button>
      </div>

      <!-- Back to login -->
      <div class="text-center text-sm text-gray-500">
        <span>Or, return to </span>
        <TextLink :href="route('login')" class="text-gray-600 hover:text-blue-500">log in</TextLink>
      </div>
    </form>
  </AuthLayout>
</template>
