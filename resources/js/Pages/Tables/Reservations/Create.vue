<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from "../../../Components/InputLabel.vue";
import InputError from "../../../Components/InputError.vue";
import TextInput from "../../../Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import {watch} from "vue";
import PrimaryButton from "../../../Components/PrimaryButton.vue";

const props = defineProps({
    table: {
        type: Number,
        required: true,
    }
})

const form = useForm({
    date: '',
});

watch(() => form.date, () => {
    form.clearErrors();
});

function submit() {
    form.post(route('tables.reservations.store', props.table));
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tische
            </h2>
        </template>

        <div class="py-12">
            <form @submit.prevent="submit">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                    <div>
                        <InputLabel for="date"  :class="{'text-red-500': form.errors.date}" value="Datum" />
                        <TextInput id="date" type="date" class="mt-1 block w-full" :class="{'border border-red-500 text-red-500': form.errors.date}" v-model="form.date" />
                        <InputError :message="form.errors.date" for="date" class="mt-2" />
                    </div>
                    <PrimaryButton type="submit">Buchen</PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
