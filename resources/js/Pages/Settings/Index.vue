<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RoomForm from "../../Components/RoomForm.vue";
import TableForm from "../Tables/Partials/TableForm.vue";
import {Link, router, useForm} from "@inertiajs/vue3";
import ConfirmationModal from "../../Components/ConfirmationModal.vue";
import {ref} from "vue";
import DangerButton from "../../Components/DangerButton.vue";
import SecondaryButton from "../../Components/SecondaryButton.vue";

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

const formDeleteTable = useForm({});
const formDeleteRoom = useForm({});

function deleteTable(id) {
    formDeleteTable.delete(route('tables.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteTableModal.value = null;
        },
    })
}


function deleteRoom(id) {
    formDeleteRoom.delete(route('rooms.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteRoomModal.value = null;
        },
    })
}

const showDeleteTableModal = ref(null);
const showDeleteRoomModal = ref(null);
</script>

<template>
    <AppLayout title="Settings">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                    <div class="bg-background-light-dark rounded-lg p-6" v-for="room in rooms.data" :key="room.id">
                        <div class="text-center flex items-center justify-center">
                            <h3 class="text-2xl font-semibold" v-text="room.name" />
                        </div>
                        <div class="flex items-center justify-around text-gray-600">
                            <div class="hover:text-white cursor-pointer">
                                <Link :href="route('tables.index')">Tables</Link>
                            </div>
                            <div>|</div>
                            <div class="hover:text-white cursor-pointer">
                                <Link :href="route('tables.index')">Edit</Link>
                            </div>
                        </div>
                    </div>
                    <div class="bg-background-light-dark rounded-lg p-6 text-center flex items-center justify-center" :key="0">
                        <h3 class="text-2xl font-semibold">
                            <Link :href="route('tables.index')">New room</Link>
                        </h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-background-light-dark rounded-lg p-6">
                        <h3 class="text-2xl font-semibold">New room</h3>
                        <RoomForm @submit-room="roomReload" />
                        <div class="mt-2">
                            <p v-for="room in rooms.data" class="p-1 flex items-center space-x-1">
                                <span>{{ room.name }}</span>
                                <svg @click="showDeleteRoomModal = room.id" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 hover:text-red-500 cursor-pointer">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </p>
                        </div>
                    </div>
                    <div class="bg-background-light-dark rounded-lg p-6">
                        <h3 class="text-2xl font-semibold">New table</h3>
                        <TableForm @submit-table="tableReload" :rooms="rooms" />
                        <div class="mt-2">
                            <p v-for="table in tables.data" class="p-1 flex items-center space-x-1">
                                <span>{{ table.name }}</span>
                                <svg @click="showDeleteTableModal = table.id" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 hover:text-red-500 cursor-pointer">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </p>
                        </div>
                    </div>
                </div>
                <ConfirmationModal :show="showDeleteTableModal != null" @close="showDeleteTableModal = null">
                    <template #title>
                        Delete table
                    </template>

                    <template #content>
                        <p>Are you sure you want to delete this table?</p>
                    </template>

                    <template #footer>
                        <SecondaryButton @click="showDeleteTableModal = null">
                            Cancel
                        </SecondaryButton>

                        <DangerButton
                            class="ml-3"
                            @click="deleteTable(showDeleteTableModal)"
                        >
                            Delete
                        </DangerButton>
                    </template>
                </ConfirmationModal>
                <ConfirmationModal :show="showDeleteRoomModal != null" @close="showDeleteRoomModal = null">
                    <template #title>
                        Delete room
                    </template>

                    <template #content>
                        <p>Are you sure you want to delete this room?</p>
                    </template>

                    <template #footer>
                        <SecondaryButton @click="showDeleteRoomModal = null">
                            Cancel
                        </SecondaryButton>

                        <DangerButton
                            class="ml-3"
                            @click="deleteRoom(showDeleteRoomModal)"
                        >
                            Delete
                        </DangerButton>
                    </template>
                </ConfirmationModal>
            </div>
        </div>
    </AppLayout>
</template>
