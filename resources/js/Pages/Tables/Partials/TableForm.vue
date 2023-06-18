<script setup lang="ts">
import InputLabel from "../../../Components/InputLabel.vue";
import TextInput from "../../../Components/TextInput.vue";
import InputError from "../../../Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "../../../Components/PrimaryButton.vue";
import Select from "../../../Components/Select.vue";
import {Room, Table} from "@/types/models";

const props = defineProps<Props>()

const form = useForm({
    name: props.table?.data?.name || '',
    location: props.table?.data?.location || '',
    room_id: (props.table?.data?.room_id || props.rooms.data[0]?.id).toString(),
    multiple_bookings: (props.table?.data?.multiple_bookings || false).toString(),
    time_off_type_id: props.table?.data?.time_off_type_id || 0,
});

const emit = defineEmits(['submit-table'])

function submit() {
    if (props.table) {
        form
            .transform(data => ({
                ...data,
                multiple_bookings: data.multiple_bookings === 'true',
                time_off_type_id: data.time_off_type_id === 0 ? null : data.time_off_type_id,
            }))
            .patch(route('tables.update', props.table.data.id), {
            onFinish: () => {
                emit('submit-table');
            },
        });
        return;
    }

    form
        .transform(data => ({
            ...data,
            multiple_bookings: data.multiple_bookings === 'true',
            time_off_type_id: data.time_off_type_id === 0 ? null : data.time_off_type_id,
        }))
        .post(route('tables.store'), {
        onFinish: () => {
            form.reset();
            emit('submit-table');
        },
    });
}

interface Props {
    rooms: {
        data: Room[]
    }
    table?: {
        data: Table,
    },
    timeOffTypes: Object,
}
</script>

<template>
    <div>
        <form @submit.prevent="submit">
            <div class="space-y-4">
                <div>
                    <InputLabel for="name" value="Name"/>
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        placeholder="Table 5120"
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name"/>
                </div>
                <div>
                    <InputLabel for="location" value="Location"/>
                    <TextInput
                        id="location"
                        v-model="form.location"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Table 5120"
                        autocomplete="location"
                    />
                    <InputError class="mt-2" :message="form.errors.location"/>
                </div>
                <div>
                    <InputLabel for="room_id" value="Room"/>
                    <Select
                        id="room_id"
                        v-model="form.room_id"
                        class="mt-1 block w-full"
                        required
                    >
                        <option v-for="(room, index) in rooms.data" :selected="index === 1" :value="room.id" :key="room.id" v-text="room.name" />
                    </Select>
                    <InputError class="mt-2" :message="form.errors.location"/>
                </div>
                <div>
                    <InputLabel for="multiple_bookings" value="Multiple bookable"/>
                    <Select
                        id="room_id"
                        v-model="form.multiple_bookings"
                        class="mt-1 block w-full"
                        required
                    >
                        <option :value="true" :key="true">True</option>
                        <option selected :value="false" :key="false">False</option>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.multiple_bookings"/>
                </div>
                <div>
                    <InputLabel for="time_off_type_id" value="Personio Time off Type"/>
                    <Select
                        id="room_id"
                        v-model="form.time_off_type_id"
                        class="mt-1 block w-full"
                        required
                    >
                        <option :value="0" :key="0">None</option>
                        <option v-for="(timeOffType, index) in timeOffTypes" :value="index" :key="index" v-text="timeOffType" />
                    </Select>
                    <InputError class="mt-2" :message="form.errors.time_off_type_id"/>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
