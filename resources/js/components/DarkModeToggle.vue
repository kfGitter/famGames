<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Moon, Sun } from 'lucide-vue-next';

const isDark = ref(false);

onMounted(() => {
    const theme = localStorage.getItem('theme') ?? 'light';
    isDark.value = theme === 'dark';
    document.documentElement.classList.toggle('dark', isDark.value);
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};
// const label = computed(() => (isDark.value ? 'Dark Mode' : 'Light Mode'));

</script>

<template>
    <button
        @click="toggleTheme"
        class="relative flex items-center justify-between -ml-2 w-12 h-8 rounded-full bg-gray-300 dark:bg-gray-600 p-1 transition-all duration-300"
        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <Sun class="h-4 w-4 text-yellow-500 " :class="{ 'opacity-100': !isDark, 'opacity-0': isDark }" />
        
        <Moon class="h-4 w-4 text-gray-200 " :class="{ 'opacity-0': !isDark, 'opacity-100': isDark }" />
        <span
            class="absolute left-1 top-1 h-6 w-6 rounded-full bg-white shadow transition-transform duration-300"
            :class="isDark ? 'translate-x-6' : 'translate-x-0'"
        />
    </button>
</template>
