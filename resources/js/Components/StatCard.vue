<script setup>
defineProps({
    title: String,
    value: [String, Number],
    color: {
        type: String,
        default: 'indigo'
    }
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
        <div class="text-xs font-bold text-gray-400 uppercase mb-1">{{ title }}</div>
        <div class="text-2xl font-black" :class="{
            'text-indigo-600 dark:text-indigo-400': color === 'indigo',
            'text-green-600 dark:text-green-400': color === 'green',
            'text-blue-600 dark:text-blue-400': color === 'blue',
            'text-orange-600 dark:text-orange-400': color === 'orange',
            'text-red-600 dark:text-red-400': color === 'red',
            'text-gray-600 dark:text-gray-400': color === 'gray',
            'text-emerald-600 dark:text-emerald-400': color === 'emerald',
            'text-teal-600 dark:text-teal-400': color === 'teal',
            'text-rose-600 dark:text-rose-400': color === 'rose',
            'text-purple-600 dark:text-purple-400': color === 'purple',
            'text-violet-600 dark:text-violet-400': color === 'violet',
            'text-cyan-600 dark:text-cyan-400': color === 'cyan',
            'text-sky-600 dark:text-sky-400': color === 'sky',
        }">
            {{ value }}
        </div>
    </div>
</template>
