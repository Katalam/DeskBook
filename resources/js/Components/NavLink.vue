<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center text-sm font-bold tracking-wider uppercase leading-5 text-purple-600 focus:outline-none transition duration-150 ease-in-out'
        : 'inline-flex items-center text-sm font-bold tracking-wider uppercase leading-5 text-gray-500 hover:text-gray-400 focus:outline-none focus:text-gray-700 transition duration-150 ease-in-out';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
