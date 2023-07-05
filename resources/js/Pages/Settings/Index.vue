<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import {Link} from "@inertiajs/vue3";
import {Feature, Room, Table, Notification} from "@/types/models";

defineProps<Props>();

interface Props {
    rooms: {
        data: Room[];
    },
    tables: {
        data: Table[];
    },
    features: {
        data: Feature[];
    },
    notifications: {
        data: Notification[];
    }
}
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
                <div class="mb-4 flex items-center justify-center">
                    <span class="font-bold uppercase px-4 py-2 text-lg border border-yellow-300 rounded-sm">Rooms</span>
                </div>
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
                <div class="mb-4 flex items-center justify-center">
                    <span class="font-bold uppercase px-4 py-2 text-lg border border-yellow-300 rounded-sm">Tables</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                    <div class="bg-background-light-dark rounded-lg p-6 flex flex-col justify-between h-full"
                         v-for="table in tables.data" :key="table.id">
                        <div class="text-center flex items-center justify-center">
                            <h3 class="text-2xl font-semibold" v-text="table.name"/>
                        </div>
                        <div class="text-gray-600 text-center hover:text-white">
                            <Link :href="route('tables.edit', table.id)">Edit</Link>
                        </div>
                    </div>
                    <div class="bg-background-light-dark rounded-lg p-6 text-center flex items-center justify-center"
                         :key="0">
                        <h3 class="text-2xl font-semibold">
                            <Link :href="route('tables.create')">New table</Link>
                        </h3>
                    </div>
                </div>
                <div class="mb-4 flex items-center justify-center">
                    <span class="font-bold uppercase px-4 py-2 text-lg border border-yellow-300 rounded-sm">Features</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                    <div class="bg-background-light-dark rounded-lg p-6 flex flex-col justify-between h-full"
                         v-for="feature in features.data" :key="feature.id">
                        <div class="text-center flex items-center justify-center">
                            <h3 class="text-2xl font-semibold" v-text="feature.name"/>
                        </div>
                        <div class="text-gray-600 text-center hover:text-white">
                            <Link :href="route('features.edit', feature.id)">Edit</Link>
                        </div>
                    </div>
                    <div class="bg-background-light-dark rounded-lg p-6 text-center flex items-center justify-center"
                         :key="0">
                        <h3 class="text-2xl font-semibold">
                            <Link :href="route('features.create')">New feature</Link>
                        </h3>
                    </div>
                </div>
                <div class="mb-4 flex items-center justify-center">
                    <span class="font-bold uppercase px-4 py-2 text-lg border border-yellow-300 rounded-sm">Notifications</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                    <div class="bg-background-light-dark rounded-lg p-6 flex flex-col justify-between h-full"
                         v-for="notification in notifications.data" :key="notification.id">
                        <div class="text-center flex items-center justify-center">
                            <h3 class="text-2xl font-semibold" v-text="notification.name"/>
                        </div>
                        <div class="text-gray-600 text-center hover:text-white">
                            <Link :href="route('notifications.edit', notification.id)">Edit</Link>
                        </div>
                    </div>
                    <div class="bg-background-light-dark rounded-lg p-6 text-center flex items-center justify-center"
                         :key="0">
                        <h3 class="text-2xl font-semibold">
                            <Link :href="route('notifications.create')">New notification</Link>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
