<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RoomForm from "../Components/RoomForm.vue";
import TableForm from "../Components/TableForm.vue";
import {router} from "@inertiajs/vue3";

defineProps({
    rooms: {
        type: Object,
        required: true,
    },
    tables: {
        type: Object,
        required: true,
    },
});

function roomReload() {
    router.reload({only: ['rooms'], preserveScroll: true})
}
function tableReload() {
    router.reload({only: ['tables'], preserveScroll: true})
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-background-light-dark rounded-lg p-6">
                        <h3 class="text-2xl font-semibold">New room</h3>
                        <RoomForm @submit-room="roomReload" />
                        <div class="mt-2">
                            <p v-for="room in rooms.data" v-text="room.name" class="p-1"></p>
                        </div>
                    </div>
                    <div class="bg-background-light-dark rounded-lg p-6">
                        <h3 class="text-2xl font-semibold">New table</h3>
                        <TableForm @submit-table="tableReload" :rooms="rooms" />
                        <div class="mt-2">
                            <p v-for="table in tables.data" v-text="table.name" class="p-1"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
