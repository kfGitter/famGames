<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    family_name: '',
});

const submit = () => {
     console.log('Submitting:', form);
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <!-- Name -->
                <div class="grid gap-2">
                    <Label for="name" class="text-gray-600">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        class="!bg-gray-50 text-black"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        v-model="form.name"
                        placeholder="Full name"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-600">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        class="!bg-gray-50 text-black"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <Label for="password" class="text-gray-600">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        class="!bg-gray-50 text-black"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <!-- Password Confirmation -->
                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-gray-600">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        class="!bg-gray-50 text-black"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <!-- Family Name -->
                <div class="grid gap-2">
                    <label for="family_name" class="text-gray-600">Family Name</label>
                    <Input
                        id="family_name"
                        v-model="form.family_name"
                        type="text"
                        required
                        :tabindex="5"
                        autocomplete="family-name"
                        placeholder="e.g. HappySmiths"
                        class=" !bg-gray-50 text-black"
                    />
                    <div v-if="form.errors.family_name" class="text-sm text-red-500">{{ form.errors.family_name }}</div>
                </div>

                <!-- Submit Button -->
                <Button type="submit" class="mt-2 w-full bg-blue-200 text-blue-900" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="text-gray-600 underline underline-offset-4 hover:text-blue-500" :tabindex="6"
                    >Log in</TextLink
                >
            </div>
        </form>
    </AuthBase>
</template>
