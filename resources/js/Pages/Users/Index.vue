<script setup>
import AppLayout from "../../Layouts/AppLayout.vue";

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AppLayout title="Users">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100">
                <div class="space-y-12">
                </div>
            </div>
        </div>
    </AppLayout>
</template>
