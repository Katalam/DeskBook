<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Profile
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 mt-10 space-y-4">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation" class="bg-background-light-dark rounded-lg p-4">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword" class="bg-background-light-dark rounded-lg p-4">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />
                </div>

                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication" class="bg-background-light-dark rounded-lg p-4">
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />
                </div>

                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0 bg-background-light-dark rounded-lg p-4" />

                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <DeleteUserForm class="mt-10 sm:mt-0 bg-background-light-dark rounded-lg p-4" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
