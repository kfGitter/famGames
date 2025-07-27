<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <GuestLayout>
        <div class="relative min-h-screen w-full bg-cover bg-center bg-no-repeat" style="background-image: url('/bg6.png')">
            <div class="absolute inset-0 bg-black/25 backdrop-blur-[3px] z-0"></div>
            <div class="relative z-10 flex min-h-screen flex-col items-center justify-center text-center">
                <!-- Your heading, text, and buttons here -->

                <div class="bg-opacity-80 max-w-xl rounded bg-white p-8 shadow-md">
                    <h1 class="mb-4 text-4xl font-bold text-gray-800">The best experiences are often offline!</h1>
                    <p class="mb-6 text-lg text-gray-700">Start tracking your family games now, share fun moments, and celebrate your wins!</p>

                    <div class="flex justify-center gap-4">
                        <Link
                            href="/login"
                            class="rounded bg-blue-200 px-6 py-2 font-medium text-blue-800 transition hover:bg-blue-300 hover:text-blue-900"
                        >
                            Login
                        </Link>

                        <Link
                            href="/register"
                            class="rounded bg-blue-800 px-6 py-2 font-medium text-blue-100 transition hover:bg-blue-900 hover:text-white"
                        >
                            Register
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
