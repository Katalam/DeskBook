<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    tables: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tische
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                <div v-for="table in $props.tables.data" :class="{'line-through': table.reserved}"
                     class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3">
                    <div class="flex justify-between">
                        <p v-text="table.name" class="font-bold uppercase mb-2 text-lg"/>
                        <div>
                            <Link :href="route('tables.reservations.create', table.id)" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Buchen</Link>
                        </div>
                    </div>
                    <p v-text="table.location" class="tracking-widest text-gray-600 font-light mb-4"/>
                    <div class="flex space-x-4 ml-3 text-gray-800 text-sm" v-for="reservation in table.reservations">
                        <div v-text="reservation.date"/>
                        <div v-text="reservation.user.name"/>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
