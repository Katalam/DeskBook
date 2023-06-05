<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    reservation: {
        type: Object,
        default: () => ({})
    }
})
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100 flex flex-col items-center sm:justify-center">
                <div class="w-full sm:max-w-md mt-6 px-12 py-10 bg-background-light-dark relative border-double sm:rounded-lg text-center text-4xl font-black tracking-widest">
                    <div class="border-triple"></div>
                    <div>
                        <h1 v-if="reservation">
                            Your table for today:<br>{{ reservation.data.table.name }}. <br>
                            <span v-if="reservation.data.table.location">It is located at {{ reservation.data.table.location }}.</span>
                        </h1>
                        <h1 v-else>You don't have a table reservation for today.</h1>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
