<script setup lang="ts">
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import {Feature, Room, Table} from "@/types/models";
import {Link, router, useForm} from "@inertiajs/vue3";
import type {Ref} from 'vue'
import {ref} from "vue";
import RoomForm from "@/Components/RoomForm.vue";
import FeatureForm from "@/Pages/Features/Partials/FeatureForm.vue";
</script>

<template>
    <AppLayout title="Feature Settings">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Create feature
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100">
                <div class="bg-background-light-dark rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl font-semibold">New feature</h3>
                    </div>
                    <FeatureForm />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
