<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RoomForm from "../../Components/RoomForm.vue";
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
                    <div class="bg-background-light-dark rounded-lg p-6 flex flex-col justify-between h-full"
                         v-for="room in rooms.data" :key="room.id">
                        <div class="text-center flex items-center justify-center">
                            <h3 class="text-2xl font-semibold" v-text="room.name"/>
                        </div>
                        <div class="text-gray-600 text-center hover:text-white">
                            <Link :href="route('rooms.edit', room.id)">Edit</Link>
                        </div>
                    </div>
                    <div class="bg-background-light-dark rounded-lg p-6 text-center flex items-center justify-center"
                         :key="0">
                        <h3 class="text-2xl font-semibold">
                            <Link :href="route('rooms.create')">New room</Link>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
