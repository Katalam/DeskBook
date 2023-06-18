<script setup lang="ts">
import TableForm from "@/Pages/Tables/Partials/TableForm.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import {Feature, Room} from "@/types/models";

const props = defineProps<Props>();
interface Props {
    rooms: {
        data: Room[];
    },
    features: {
        data: Feature[],
    },
    timeOffTypes: Object,
}
</script>

<template>
    <AppLayout title="Table Settings">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Create table
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100">
                <div class="bg-background-light-dark rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl font-semibold">Create table</h3>
                    </div>
                    <TableForm :rooms="rooms" :features="features" :time-off-types="timeOffTypes" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
