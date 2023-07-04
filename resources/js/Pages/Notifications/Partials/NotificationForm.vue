<script setup lang="ts">
import {useForm} from "@inertiajs/vue3";
import {Notification} from "@/types/models";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps<Props>()


const form = useForm({
    name: props.notification?.data?.name || '',
});

function submit() {
    if (props.notification) {
        form.patch(route('features.update', props.notification.data.id));
        return;
    }

    form.post(route('features.store'));
}

interface Props {
    notification?: {
        data: Notification
    }
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
                        placeholder="Height adjustment"
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name"/>
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
