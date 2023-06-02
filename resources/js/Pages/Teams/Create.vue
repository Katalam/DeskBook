<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTeamForm from '@/Pages/Teams/Partials/CreateTeamForm.vue';
</script>

<template>
    <AppLayout title="Create Team">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Create Team
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 mt-10 space-y-4">
                <div class="bg-background-light-dark rounded-lg p-4">
                    <CreateTeamForm/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
