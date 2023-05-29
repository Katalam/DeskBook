<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {Link, router} from '@inertiajs/vue3';
import axios from "axios";

const props = defineProps({
    rooms: {
        type: Object,
        required: true,
    },
    dates: {
        type: Object,
        required: true,
    }
});

function reserve(tableId) {
    const data = {
        date: props.dates.selectedDate,
    };

    axios.post(route('tables.reservations.store', tableId), data)
        .then(() => {
            router.reload({only: ['rooms']})
        });
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Tables
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100">
                <div class="flex justify-center sm:justify-end">
                    <div class="flex items-center justify-between border border-pink-800 text-lg px-3 py-1 rounded-sm">
                        <Link :href="route('tables.index', {'date': dates.before})" class="px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                            </svg>
                        </Link>
                        <div v-text="dates.selectedDate" class="px-2"/>
                        <Link :href="route('tables.index', {'date': dates.after})" class="px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </Link>
                    </div>
                </div>
                <div class="space-y-12">
                    <div v-for="room in $props.rooms.data">
                        <div class="mb-4 flex items-center justify-center">
                            <span v-text="room.name"
                                  class="font-bold uppercase px-4 py-2 text-lg border border-yellow-300 rounded-sm"/>
                        </div>
                        <div class="gap-4 grid grid-cols-1 sm:grid-cols-3">
                            <div v-for="table in room.tables"
                                 class="overflow-hidden bg-background-light-dark rounded-lg p-4">
                                <div>
                                    <div class="flex flex-col justify-end">
                                        <p v-text="table.name" class="text-center font-bold uppercase text-lg"
                                           :class="{'line-through': table.reserved}"/>
                                        <p v-if="table.reservation" v-text="table.reservation.user.name"
                                           class="uppercase no-underline text-center"/>
                                        <p v-text="table.location" :class="{'line-through': table.reserved}"
                                           class="tracking-widest text-gray-600 font-light mb-4 text-center"/>
                                        <div class="flex items-center justify-center">
                                            <button type="button"
                                                    @click="reserve(table.id)"
                                                    v-if="!table.reserved"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-700 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Reservieren
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
