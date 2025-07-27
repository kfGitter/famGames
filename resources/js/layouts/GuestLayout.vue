<script setup></script>

<template>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
<div class="w-16 bg-blue-900 text-white flex flex-col items-center pt-4 fixed top-0 left-0 h-screen">
            <img src="/famlogo.png" alt="Logo" class="h-15 w-15" />
        </div>

        <!-- Page content -->
        <main class="ml-16 w-full">
            <slot />
        </main>
    </div>
</template>
