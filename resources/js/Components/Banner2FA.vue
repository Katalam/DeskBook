<script setup>
import {computed, ref, watch} from 'vue';
import {usePage} from '@inertiajs/vue3';

const show = ref(true);
const enabled = computed(() => usePage().props.jetstream.flash?.twofa || false);

watch(enabled, async () => {
    show.value = true;
});
</script>

<template>
    <div>
        <div v-if="show && enabled"
             class="bg-purple-500/50">
            <div class="max-w-screen-xl mx-auto py-2 px-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center min-w-0">
                        <span class="flex p-2 rounded-lg bg-purple-600/50">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                            </svg>
                        </span>

                        <p class="ml-3 font-medium text-sm text-white truncate">
                            Please enable two factor authentication to secure your account.
                        </p>
                    </div>

                    <div class="shrink-0 sm:ml-3">
                        <button
                            type="button"
                            class="-mr-1 flex p-2 rounded-md focus:outline-none sm:-mr-2 transition hover:bg-purple-600/50 focus:bg-purple-600/50"
                            aria-label="Dismiss"
                            @click.prevent="show = false"
                        >
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
