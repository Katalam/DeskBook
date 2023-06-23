<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import {Link, router, usePage} from '@inertiajs/vue3';
import {Room, Table} from "@/types/models";
import {computed, ref, watch} from "vue";
import TableReserve from "@/Pages/Tables/Partials/TableReserve.vue";
import {VTooltip} from "floating-vue";
import Tooltip from "@/Components/Tooltip.vue";

const props = defineProps<Props>()

function reloadRooms(): void {
    router.reload({only: ['rooms', 'hasBookedSelectedDate']})
}

window.Echo.private(`tables.${usePage().props.auth.user.current_team_id}`)
    .listen('TableReserved', (e: any) => {
        router.reload({only: ['rooms', 'hasBookedSelectedDate']})
    })


const search = ref(props.oldQuery ?? '');

watch(search, () => {
    if (search.value.length === 0) {
        setURLSearchParam('search', null);
    }

    setURLSearchParam('search', search.value);
});


function setURLSearchParam(key: string, value: string | null) {
    const url = new URL(window.location.href);
    if (value === null) {
        const url = removeParamFromURL(window.location.href, key);
        // full page reload to remove the search param from the url
        router.visit(url, {
            preserveScroll: true,
            preserveState: true
        })

        return;
    }

    url.searchParams.set(key, value);
    window.history.pushState(null, '', url.href);
}

function removeParamFromURL(url: string, param: string) {
    const [path, searchParams] = url.split('?');
    const newSearchParams = searchParams
        ?.split('&')
        .filter((p) => !(p === param || p.startsWith(`${param}=`)))
        .join('&');
    return newSearchParams ? `${path}?${newSearchParams}` : path;
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'k') {
        const elem: HTMLElement | null = document.querySelector('#after');
        elem?.click();
    } else if (e.key === 'j') {
        const elem: HTMLElement | null = document.querySelector('#before');
        elem?.click();
    }
});

const rooms = computed(() => {
    // Make a deep copy of the rooms data
    const rooms = JSON.parse(JSON.stringify(props.rooms.data));

    return rooms.filter((room: Room) => {
        const filteredTables = room.tables.filter((table: Table) => {
            return table.name.toLowerCase().includes(search.value.toLowerCase());
        });

        // Replace the room's tables array with the filtered tables
        room.tables = filteredTables;

        // Return the modified room object only if it has matching tables
        return filteredTables.length > 0;
    });
});

interface Props {
    rooms: Rooms,
    dates: Dates,
    hasBookedSelectedDate: boolean,
    oldQuery?: string,
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
            <div class="max-w-7xl mx-auto space-y-4 text-gray-100 relative">
                 <div class="flex sm:flex-row flex-col justify-center sm:justify-between gap-2 sticky top-2 z-10">
                    <div class="flex sm:flex-row flex-col justify-center gap-2 bg-background">
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
                            class="flex items-center justify-center border border-pink-800 text-lg px-3 py-1 rounded-sm bg-background"
                            v-if="!dates.isToday">
                            <Link :href="route('tables.index', {'date': dates.today})" class="px-2">
                                Today
                            </Link>
                        </div>

                        <div
                            class="flex items-center justify-between border border-pink-800 text-lg px-3 py-1 rounded-sm bg-background">
                            <Tooltip>
                                <template #trigger>
                                    <Link id="before"
                                          :href="route('tables.index', {'date': dates.before})"
                                          class="px-2 h-min"
                                          preserve-scroll>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5"
                                             stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15.75 19.5L8.25 12l7.5-7.5"/>
                                        </svg>
                                    </Link>
                                </template>
                                <template #content>
                                    <span class="text-sm">Press <kbd>j</kbd> to go to the previous day</span>
                                </template>
                            </Tooltip>
                            <div v-text="dates.selectedWeekday" class="px-2"/>
                            <div v-text="dates.selectedDate" class="px-2"/>
                            <Tooltip>
                                <template #trigger>
                                    <Link id="after"
                                          :href="route('tables.index', {'date': dates.after})"
                                          class="px-2"
                                          preserve-scroll>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5"
                                             stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                        </svg>
                                    </Link>
                                </template>
                                <template #content>
                                    <span class="text-sm">Press <kbd>k</kbd> to go to the next day</span>
                                </template>
                            </Tooltip>

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
                                <TableReserve :key="table.id" :table="table" :selected-date="dates.selectedDate"
                                              @reload-rooms="reloadRooms"
                                              :has-booked-selected-date="hasBookedSelectedDate"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
