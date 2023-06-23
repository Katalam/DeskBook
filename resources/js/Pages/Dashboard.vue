<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import {Reservation, Table} from "@/types/models";
import DangerButton from "@/Components/DangerButton.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import {RouteParam} from "ziggy-js";
import TableReserve from "@/Pages/Tables/Partials/TableReserve.vue";

const props = defineProps<Props>();


window.Echo.private(`tables.${usePage().props.auth.user.current_team_id}`)
    .listen('TableReserved', (e: any) => {
        router.reload({only: ['favorites']})
    })

function reloadRooms(): void {
    router.reload({only: ['reservation', 'favorites', 'hasBookedSelectedDate']})
}

interface Props {
    reservation?: {
        data: Reservation,
    },
    favorites: {
        data: Array<Table>,
    },
    today: string,
    hasBookedSelectedDate: boolean,
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
            <div class="max-w-7xl mx-auto text-gray-100 flex flex-col items-center sm:justify-center">
                <div
                    class="w-full sm:max-w-md mt-6 px-12 py-10 bg-background-light-dark relative border-double sm:rounded-lg text-center text-4xl font-black tracking-widest">
                    <div class="border-triple"></div>
                    <div>
                        <h1 v-if="reservation">
                            Your table for today:<br>{{ reservation.data.table?.name }}. <br>
                            <span v-if="reservation.data.table?.location">It is located at {{
                                    reservation.data.table?.location
                                }}.</span>
                        </h1>
                        <h1 v-else>You don't have a table reservation for today.</h1>
                    </div>
                </div>
                <div class="w-full mt-20 grid grid-cols-1 gap-2" :class="{'sm:grid-cols-2': favorites.data.length > 1}">
                    <div v-for="table in favorites.data"
                         class="overflow-hidden bg-background-light-dark rounded-lg p-4">
                        <TableReserve @reload-rooms="reloadRooms"
                                      :has-booked-selected-date="hasBookedSelectedDate"
                                      :selected-date="today"
                                      :table="table"/>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
