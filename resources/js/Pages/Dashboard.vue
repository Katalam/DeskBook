<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import {Reservation, Table} from "@/types/models";
import DangerButton from "@/Components/DangerButton.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import {RouteParam} from "ziggy-js";

const props = defineProps<Props>();


window.Echo.private(`tables.${usePage().props.auth.user.current_team_id}`)
    .listen('TableReserved', (e: any) => {
        router.reload({only: ['favorites']})
    })

function reserve(tableId: RouteParam) {
    const data = {
        date: props.today
    };

    window.axios.post(route('tables.reservations.store', tableId), data)
        .then(() => {
            router.reload({only: ['rooms', 'hasBookedSelectedDate']})
        });
}

const deleteForm = useForm({});

function deleteReserved(tableId: RouteParam, reservationId: RouteParam) {
    deleteForm.delete(route('tables.reservations.destroy', [tableId, reservationId]), {
        preserveScroll: true,
    })
}

function canBookTable(table: Table): boolean {
    return (
            !table.reserved
            || (
                table.multiple_bookings
                && !table.reservations.map((reservation: Reservation) => reservation.user.id).includes(usePage().props.auth.user.id)
            )
        )
        && !props.reservation
}

interface Props {
    reservation?: {
        data: Reservation,
    },
    favorites: {
        data: Array<Table>,
    },
    today: string,
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
                            <span v-if="reservation.data.table?.location">It is located at {{ reservation.data.table?.location }}.</span>
                        </h1>
                        <h1 v-else>You don't have a table reservation for today.</h1>
                    </div>
                </div>
                <div class="w-full mt-20 grid grid-cols-1 gap-2" :class="{'sm:grid-cols-2': favorites.data.length > 1}">
                    <div v-for="table in favorites.data"
                         class="overflow-hidden bg-background-light-dark rounded-lg p-4">
                        <div class="flex flex-col justify-between h-full">
                            <div>
                                <p v-text="table.name" class="text-center font-bold uppercase text-lg"
                                   :class="{'line-through': table.reserved && !table.multiple_bookings}"/>
                                <p v-text="table.location"
                                   :class="{'line-through': table.reserved && !table.multiple_bookings}"
                                   class="tracking-widest text-gray-600 font-light mb-4 text-center"/>
                                <p v-text="table?.room_name"
                                   :class="{'line-through': table.reserved && !table.multiple_bookings}"
                                   class="tracking-widest text-gray-400 font-light mb-4 text-center"/>
                                <div v-for="reservation in table.reservations">
                                    <p v-if="reservation" v-text="reservation.user.name"
                                       class="uppercase no-underline text-center"/>
                                </div>
                            </div>
                            <div class="flex items-center justify-center mt-2">
                                <template v-for="reservation in table.reservations" v-if="table.reserved">
                                    <DangerButton
                                        @click="deleteReserved(table.id, reservation.id)"
                                        v-if="reservation.user.id === $page.props.auth.user.id"
                                        class="inline-flex items-center px-4 py-2 bg-blue-500 rounded-2xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Cancel
                                    </DangerButton>
                                </template>
                                <button type="button"
                                        @click="reserve(table.id)"
                                        v-if="canBookTable(table)"
                                        class="inline-flex items-center px-4 py-2 bg-blue-500 rounded-2xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Reserve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
