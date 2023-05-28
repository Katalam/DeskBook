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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tische
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                <div class="flex justify-end">
                    <div class="flex items-center justify-between">
                        <Link :href="route('tables.index', {'date': dates.before})" class="px-1">&lt;</Link>
                        <div v-text="dates.selectedDate"/>
                        <Link :href="route('tables.index', {'date': dates.after})" class="px-1">&gt;</Link>
                    </div>
                </div>
                <div v-for="room in $props.rooms.data" class="space-y-4">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3">
                        <p v-text="room.name" class="font-bold uppercase text-lg"/>
                    </div>
                    <div v-for="table in room.tables"
                         class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3">
                        <div class="flex justify-between">
                            <div class="flex items-center justify-between gap-8 mb-2">
                                <p v-text="table.name" class="font-bold uppercase text-lg" :class="{'line-through': table.reserved}"/>
                                <p v-if="table.reservation" v-text="table.reservation.user.name" class="uppercase no-underline"/>
                            </div>
                            <button type="button"
                                    @click="reserve(table.id)"
                                    v-if="!table.reserved"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :disabled="table.reserved">Reservieren
                            </button>
                        </div>
                        <p v-text="table.location" :class="{'line-through': table.reserved}" class="tracking-widest text-gray-600 font-light mb-4"/>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
