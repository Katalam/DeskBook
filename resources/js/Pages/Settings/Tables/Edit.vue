<script setup lang="ts">
import TableForm from "@/Pages/Tables/Partials/TableForm.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import {Room, Table} from "@/types/models";
import {router, useForm} from "@inertiajs/vue3";
import type {Ref} from "vue";
import {ref} from "vue";

const props = defineProps<Props>();

const showDeleteTableModal : Ref<Boolean> = ref(false);

function deleteTable() {
    useForm({}).delete(route('tables.destroy', props.table.data.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteTableModal.value = false;
        },
    })
}

function reloadTable() {
    router.reload({only: ['table'], preserveScroll: true})
}

interface Props {
    table: {
        data: Table;
    },
    rooms: {
        data: Room[];
    },
    timeOffTypes: Object,
}
</script>

<template>
    <AppLayout title="Table Settings">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Edit table
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100">
                <div class="bg-background-light-dark rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl font-semibold">{{ table.data.name }}</h3>
                        <svg @click="showDeleteTableModal = true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 hover:text-red-500 cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </div>
                    <TableForm :table="table" @submit-table="reloadTable" :rooms="rooms" :time-off-types="timeOffTypes" />
                </div>
                <ConfirmationModal :show="showDeleteTableModal" @close="showDeleteTableModal = false">
                    <template #title>
                        Delete table
                    </template>

                    <template #content>
                        <p>Are you sure you want to delete this table?</p>
                    </template>

                    <template #footer>
                        <SecondaryButton @click="showDeleteTableModal = false">
                            Cancel
                        </SecondaryButton>

                        <DangerButton
                            class="ml-3"
                            @click="deleteTable"
                        >
                            Delete
                        </DangerButton>
                    </template>
                </ConfirmationModal>
            </div>
        </div>
    </AppLayout>
</template>
