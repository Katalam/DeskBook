<script setup lang="ts">
import AppLayout from "../../Layouts/AppLayout.vue";
import {User} from "@/types/models";
import DangerButton from "@/Components/DangerButton.vue";
import {useForm} from "@inertiajs/vue3";
import {RouteParam} from "ziggy-js";

const props = defineProps<{
    users: {
        data: User[],
    },
}>();

function deleteReserved(tableId: RouteParam, reservationId: RouteParam) {
    useForm({}).delete(route('tables.reservations.destroy', [tableId, reservationId]), {
        preserveScroll: true,
    })
}
</script>

<template>
    <AppLayout title="Users">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-8 text-gray-100">
                <div v-for="user in users.data">
                    <div class="mb-4 flex items-center justify-center" v-if="user.reservations?.length > 0">
                            <span v-text="user.name"
                                  class="font-bold uppercase px-4 py-2 text-lg border border-yellow-300 rounded-sm"/>
                    </div>
                    <div class="gap-4 grid grid-cols-1 sm:grid-cols-3" v-if="user.reservations?.length > 0">
                        <div
                            class="flex flex-col justify-between h-full overflow-hidden bg-background-light-dark rounded-lg p-4"
                            v-for="reservation in user?.reservations">
                            <div class="text-center font-bold text-xl" v-text="reservation.date"/>
                            <div>
                                <div class="relative">
                                    <p v-text="reservation.table?.name"
                                       class="text-center font-semibold uppercase"/>
                                </div>
                                <p v-text="reservation.table?.location"
                                   class="tracking-widest text-gray-600 font-light mb-4 text-center"/>
                            </div>
                            <div class="flex items-center justify-center mt-2">
                                <DangerButton
                                    @click="deleteReserved(reservation.table?.id ?? 0, reservation.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-500 rounded-2xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </DangerButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
