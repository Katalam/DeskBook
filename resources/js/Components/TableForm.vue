<script setup>
import InputLabel from "./InputLabel.vue";
import TextInput from "./TextInput.vue";
import InputError from "./InputError.vue";
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "./PrimaryButton.vue";
import Select from "./Select.vue";

const props = defineProps({
    rooms: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    name: '',
    location: '',
    room_id: props.rooms.data[0]?.id?.toString() ?? '',
    multiple_bookings: false,
});

const emit = defineEmits(['submit-table'])

function submit() {
    form
        .transform(data => ({
            ...data,
            room_id: parseInt(data.room_id),
        }))
        .post(route('tables.store'), {
        onFinish: () => {
            form.reset();
            emit('submit-table');
        },
    });

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
                        required
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
                        <option  :value="true" :key="true">True</option>
                        <option selected :value="false" :key="false">False</option>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.multiple_bookings"/>
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
