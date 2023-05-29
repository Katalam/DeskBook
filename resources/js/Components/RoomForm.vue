<script setup>
import InputLabel from "./InputLabel.vue";
import TextInput from "./TextInput.vue";
import InputError from "./InputError.vue";
import {useForm} from "@inertiajs/vue3";
import PrimaryButton from "./PrimaryButton.vue";

const form = useForm({
    name: '',
});

const emit = defineEmits(['submit-room'])

function submit() {
    form.post(route('rooms.store'), {
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
            <div>
                <InputLabel for="name" value="Name"/>
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    placeholder="Room 404"
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name"/>
            </div>
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
