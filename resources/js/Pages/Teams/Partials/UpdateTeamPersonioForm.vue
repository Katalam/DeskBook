<script setup lang="ts">
import {InertiaForm, useForm} from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Team, TeamPermissions} from "@/types/models";
import {PropType} from "vue";

const props = defineProps<{
    team: Team,
    permissions: TeamPermissions,
}>();

const form : InertiaForm<{
    personio_client_id: string;
    personio_client_secret: string
}> = useForm({
    personio_client_id: props.team.personio_client_id ?? '',
    personio_client_secret: props.team.personio_client_secret ?? '',
});

const updateTeamPersonio = () => {
    form.put(route('teams.personio.update', props.team), {
        errorBag: 'updateTeamPersonio',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updateTeamPersonio">
        <template #title>
            Personio
        </template>

        <template #description>
            Configure Personio for your team.
        </template>

        <template #form>
            <!-- Personio Client id -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="personio_client_id" value="Personio Client Id" />

                <TextInput
                    id="personio_client_id"
                    v-model="form.personio_client_id"
                    type="password"
                    class="mt-1 block w-full"
                    :disabled="! permissions.canUpdateTeam"
                />

                <InputError :message="form.errors.personio_client_id" class="mt-2" />
            </div>

            <!-- Personio Client Secret -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="personio_client_secret" value="Personio Client Secret" />

                <TextInput
                    id="personio_client_secret"
                    v-model="form.personio_client_secret"
                    type="password"
                    class="mt-1 block w-full"
                    :disabled="! permissions.canUpdateTeam"
                />

                <InputError :message="form.errors.personio_client_secret" class="mt-2" />
            </div>
        </template>

        <template v-if="permissions.canUpdateTeam" #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
