<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <GuestLayout>
    <div class="relative min-h-screen w-full bg-cover bg-center bg-no-repeat" style="background-image: url('/bg6.png')">
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>

      <div class="relative z-10 flex min-h-screen flex-col items-center justify-center text-center px-4">
        <!-- Central card -->
        <div class="relative z-10 max-w-xl rounded-2xl bg-white/90 p-10 shadow-xl backdrop-blur-sm">
          <h1 class="mb-4 text-4xl font-extrabold text-gray-800 drop-shadow-sm">
            The best experiences are often offline!
          </h1>
          <p class="mb-6 text-lg text-gray-700">
            Start tracking your family games now, share fun moments, and celebrate your wins!
          </p>

          <div class="flex justify-center gap-4 flex-wrap">
            <Link
              href="/login"
              class="rounded-lg bg-blue-200 px-6 py-3 font-semibold text-blue-800 transition duration-300 hover:bg-blue-300 hover:text-blue-900"
            >
              Login
            </Link>

            <Link
              href="/register"
              class="rounded-lg bg-blue-800 px-6 py-3 font-semibold text-white transition duration-300 hover:bg-blue-900"
            >
              Register
            </Link>
          </div>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>
