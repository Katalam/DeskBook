<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import {Link, router, useForm, usePage} from '@inertiajs/vue3';
import DangerButton from "../../Components/DangerButton.vue";
import {RouteParam} from "ziggy-js";
import {Reservation, Room, Table} from "@/types/models";
import {computed, ref} from "vue";

const props = defineProps<Props>()

function reserve(tableId: RouteParam) {
    const data = {
        date: props.dates.selectedDate,
    };

    window.axios.post(route('tables.reservations.store', tableId), data)
        .then(() => {
            router.reload({only: ['rooms', 'hasBookedSelectedDate']})
        });
}

function toggleFavorite(tableId: RouteParam) {
    window.axios.post(route('tables.favorite.toggle', tableId))
        .then(() => {
            router.reload({only: ['rooms']})
        });

    // we fake the toggle here to avoid waiting for the api call to finish
    props.rooms.data.forEach((room: Room) => {
        room.tables.forEach((table: Table) => {
            if (table.id === tableId) {
                table.is_favorite = !table.is_favorite
            }
        })
    });
}

const deleteForm = useForm({});

function deleteReserved(tableId: RouteParam, reservationId: RouteParam) {
    deleteForm.delete(route('tables.reservations.destroy', [tableId, reservationId]), {
        preserveScroll: true,
    })
}

window.Echo.private(`tables.${usePage().props.auth.user.current_team_id}`)
    .listen('TableReserved', (e: any) => {
        router.reload({only: ['rooms', 'hasBookedSelectedDate']})
    })

function canBookTable(table: Table): boolean {
    return (
            !table.reserved
            || (
                table.multiple_bookings
                && !table.reservations.map((reservation: Reservation) => reservation.user.id).includes(usePage().props.auth.user.id)
            )
        )
        && !props.hasBookedSelectedDate
}

const search = ref('');

const rooms = computed(() => {
    return props.rooms.data.filter((room: Room) => {
        return room.tables.filter((table: Table) => {
            return table.name.toLowerCase().includes(search.value.toLowerCase())
        }).length > 0
    })
})

interface Props {
    rooms: Rooms,
    dates: Dates,
    hasBookedSelectedDate: boolean,
}

declare interface Rooms {
    data: Array<Room>
}

declare interface Dates {
    today: string,
    before: string,
    after: string,
    selectedDate: string,
    selectedWeekday: string,
    isToday: boolean,
}
</script>

<template>
    <AppLayout title="Tables">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Tables
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100">
                <div class="flex sm:flex-row flex-col justify-center sm:justify-between gap-2">
                    <div class="flex sm:flex-row flex-col justify-center gap-2">
                        <div
                            class="flex items-center justify-center border border-pink-800 text-lg px-3 py-1 rounded-sm">
                            <input
                                ref="input"
                                class="border-gray-300 text-center sm:text-left bg-background text-gray-200 focus:outline-none"
                                placeholder="Search for a room"
                                v-model="search"
                            >
                        </div>
                    </div>
                    <div class="flex sm:flex-row flex-col justify-center gap-2">
                        <div
                            class="flex items-center justify-center border border-pink-800 text-lg px-3 py-1 rounded-sm"
                            v-if="!dates.isToday">
                            <Link :href="route('tables.index', {'date': dates.today})" class="px-2">
                                Today
                            </Link>
                        </div>

                        <div
                            class="flex items-center justify-between border border-pink-800 text-lg px-3 py-1 rounded-sm">
                            <Link :href="route('tables.index', {'date': dates.before})" class="px-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5"
                                     stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.75 19.5L8.25 12l7.5-7.5"/>
                                </svg>
                            </Link>
                            <div v-text="dates.selectedWeekday" class="px-2"/>
                            <div v-text="dates.selectedDate" class="px-2"/>
                            <Link :href="route('tables.index', {'date': dates.after})" class="px-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5"
                                     stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
                <div class="space-y-12">
                    <div v-for="room in rooms">
                        <div class="mb-4 flex items-center justify-center">
                            <span v-text="room.name"
                                  class="font-bold uppercase px-4 py-2 text-lg border border-yellow-300 rounded-sm"/>
                        </div>
                        <div class="gap-4 grid grid-cols-1 sm:grid-cols-3">
                            <div v-for="table in room.tables"
                                 class="overflow-hidden bg-background-light-dark rounded-lg p-4">
                                <div class="flex flex-col justify-between h-full">
                                    <div>
                                        <div class="relative">
                                            <p v-text="table.name" class="text-center font-bold uppercase text-lg"
                                               :class="{'line-through': table.reserved && !table.multiple_bookings}"/>
                                            <span class="absolute top-0 right-0 hover:text-yellow-400/60" :class="{'text-gray-600': !table.is_favorite, 'text-yellow-400': table.is_favorite}" @click="toggleFavorite(table.id)">
                                                <svg xmlns="http://www.w3.org/2000/svg" :fill="table.is_favorite ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <p v-text="table.location"
                                           :class="{'line-through': table.reserved && !table.multiple_bookings}"
                                           class="tracking-widest text-gray-600 font-light mb-4 text-center"/>
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
            </div>
        </div>
    </AppLayout>
</template>
