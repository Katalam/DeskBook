<script setup>
import InputLabel from "./InputLabel.vue";
import TextInput from "./TextInput.vue";
import InputError from "./InputError.vue";
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "./PrimaryButton.vue";
import Select from "./Select.vue";

const form = useForm({
    name: '',
    is_outside: false,
});

const emit = defineEmits(['submit-room'])

function submit() {
    form
        .transform(data => ({
            ...data,
            is_outside: data.is_outside === 'true',
        }))
        .post(route('rooms.store'), {
            onFinish: () => {
                form.reset('name');
                emit('submit-room');
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
                        placeholder="Room 404"
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name"/>
                </div>
                <div>
                    <InputLabel for="is_outside" value="Outside?"/>
                    <Select
                        id="is_outside"
                        v-model="form.is_outside"
                        class="mt-1 block w-full"
                        required
                    >
                        <option selected :value="false" :key="false">False</option>
                        <option :value="true" :key="true">True</option>
                    </Select>
                    <InputError class="mt-2" :message="form.errors.is_outside"/>
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
