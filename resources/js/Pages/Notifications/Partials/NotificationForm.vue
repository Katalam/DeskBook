<script setup lang="ts">
import {InertiaForm, useForm} from "@inertiajs/vue3";
import {Notification, Room} from "@/types/models";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Select from "../../../Components/Select.vue";
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps<Props>()


const form : InertiaForm<{
    name: string
    type: string
    number: number
    channel: string
    receiver: string
    message: string
    rooms: number[]
    days: number[]
}> = useForm({
    name: props.notification?.data?.name || '',
    type: props.notification?.data?.type || 'empty',
    number: props.notification?.data?.number || 0,
    channel: props.notification?.data?.channel || 'slack',
    receiver: props.notification?.data?.receiver || '',
    message: props.notification?.data?.message || '',
    rooms: props.notification?.data?.rooms || [],
    days : props.notification?.data?.days || Object.keys(props.days).map((day) => parseInt(day)),
});

function submit() {
    if (props.notification) {
        form.patch(route('notifications.update', props.notification.data.id));
        return;
    }

    form.post(route('notifications.store'));
}

interface Props {
    notification?: {
        data: Notification
    },
    rooms: {
        data: Room[]
    },
    days: {
        [key: number]: string
    }
}
</script>

<template>
    <div class="space-y-4">
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
                        placeholder="Slack notification"
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name"/>
                </div>
                <div>
                    <InputLabel for="type" value="Type"/>
                    <Select
                        id="type"
                        v-model="form.type"
                        class="mt-1 block w-full"
                        required
                    >
                        <option :selected="form.type === 'empty'" value="empty" key="empty">Notify when room is empty</option>
                        <option :selected="form.type === 'more_than'" value="more_than" key="more_than">Notify when room has more reservations than</option>
                        <option :selected="form.type === 'less_than'" value="less_than" key="less_than">Notify when room has less reservations than</option>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.type"/>
                </div>
                <div v-show="form.type !== 'empty'">
                    <InputLabel for="number" value="Reservation Amount"/>
                    <TextInput
                        id="number"
                        v-model="form.number"
                        type="number"
                        class="mt-1 block w-full"
                        required
                        placeholder="1"
                        autocomplete="number"
                    />
                    <InputError class="mt-2" :message="form.errors.number"/>
                </div>
                <div>
                    <InputLabel for="channel" value="Channel"/>
                    <Select
                        id="channel"
                        v-model="form.channel"
                        class="mt-1 block w-full"
                        required
                    >
                        <option :selected="form.type === 'slack'" value="slack" key="slack">Slack</option>
                        <!-- not implemented yet -->
<!--                        <option :selected="form.type === 'email'" value="email" key="email">E-Mail</option>-->
                    </Select>
                    <InputError class="mt-2" :message="form.errors.channel"/>
                </div>
                <div>
                    <InputLabel for="receiver" value="Receiver"/>
                    <TextInput
                        id="receiver"
                        v-model="form.receiver"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        :placeholder="form.channel === 'slack' ? 'https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX' : 'info@test.com'"
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.receiver"/>
                </div>
                <div>
                    <InputLabel for="message" value="Message"/>
                    <TextInput
                        id="message"
                        v-model="form.message"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        placeholder="The rooms are empty! (<room_names> is a placeholder for the room names)"
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.message"/>
                </div>

                <div>
                    <InputLabel value="Rooms"/>
                    <InputLabel for="rooms">
                        <div class="flex items-center" v-for="room in props.rooms.data">
                            <Checkbox :id="'room' + room.id" v-model:checked="form.rooms" :value="room.id.toString()" :name="'room' + room.id" />
                            <div class="ml-2" v-text="room.name" />
                        </div>
                        <InputError class="mt-2" :message="form.errors.rooms" />
                    </InputLabel>
                </div>

                <div>
                    <InputLabel value="Days to notify"/>
                    <InputLabel for="days">
                        <div class="flex items-center" v-for="(day, key) in props.days">
                            <Checkbox :id="'day' + key" v-model:checked="form.days" :value="key.toString()" :name="'day' + key" />
                            <div class="ml-2" v-text="day" />
                        </div>
                        <InputError class="mt-2" :message="form.errors.days" />
                    </InputLabel>
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </div>
</template>
