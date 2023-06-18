<script setup lang="ts">
import DangerButton from "@/Components/DangerButton.vue";
import {Reservation, Table} from "@/types/models";
import {RouteParam} from "ziggy-js";
import {useForm, usePage} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps<Props>();

const emits = defineEmits<{
    reloadRooms: []
}>();

const isReserved = ref(props.table.reserved);

function reserve(tableId: RouteParam) {
    const data = {
        date: props.selectedDate,
    };

    window.axios.post(route('tables.reservations.store', tableId), data)
        .then(() => {
            emits('reloadRooms');
        });

    isReserved.value = true;
}

const isFavorite = ref(props.table.is_favorite);

function toggleFavorite(tableId: RouteParam) {
    isFavorite.value = !isFavorite.value;

    window.axios.post(route('tables.favorite.toggle', tableId))
        .then(() => {
            emits('reloadRooms');
        });
}

function deleteReserved(tableId: RouteParam, reservationId: RouteParam) {
    useForm({}).delete(route('tables.reservations.destroy', [tableId, reservationId]), {
        preserveScroll: true,
    })

    isReserved.value = false;
}

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

const featuresOpen = ref(false);

interface Props {
    table: Table,
    selectedDate: string,
    hasBookedSelectedDate: boolean,
}
</script>

<template>
    <div class="flex flex-col justify-between h-full">
        <div>
            <div class="relative">
                <p v-text="table.name" class="text-center font-bold uppercase text-lg"
                   :class="{'line-through': table.reserved && !table.multiple_bookings}"/>
                <span class="absolute top-0 right-0 hover:text-yellow-400/60"
                      :class="{'text-gray-600': !isFavorite, 'text-yellow-400': isFavorite}"
                      @click="toggleFavorite(table.id)">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         :fill="isFavorite ? 'currentColor' : 'none'"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                    </svg>
                </span>
            </div>
            <p v-text="table.location"
               :class="{'line-through': table.reserved && !table.multiple_bookings}"
               class="tracking-widest text-gray-600 font-light mb-4 text-center"/>
            <div v-for="reservation in table.reservations">
                <div class="flex items-center justify-center gap-3">
                    <div
                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                        <img class="h-8 w-8 rounded-full object-cover"
                             :src="reservation.user.profile_photo_url"
                             :alt="reservation.user.name">
                    </div>
                    <p v-if="reservation" v-text="reservation.user.name"
                       class="uppercase no-underline text-center"/>
                </div>
            </div>
        </div>
        <div class="mt-2">
            <div @click="featuresOpen = !featuresOpen">
                <div class="flex items-center gap-1 text-sm" v-show="!featuresOpen">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <span>Attributes</span>
                </div>
                <div class="flex items-center gap-1 text-sm" v-show="featuresOpen">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                    <span>Attributes</span>
                </div>
            </div>
            <div v-show="featuresOpen">
                <div class="grid grid-cols-1 gap-1 divide-y divide-gray-700">
                    <div class="text-center" v-for="feature in table.features" :key="feature.id" v-text="feature.name" />
                </div>
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
                    v-if="canBookTable(table) && !isReserved"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 rounded-2xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Reserve
            </button>
        </div>
    </div>
</template>
