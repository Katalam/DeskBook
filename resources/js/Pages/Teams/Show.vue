<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteTeamForm from '@/Pages/Teams/Partials/DeleteTeamForm.vue';
import TeamMemberManager from '@/Pages/Teams/Partials/TeamMemberManager.vue';
import UpdateTeamNameForm from '@/Pages/Teams/Partials/UpdateTeamNameForm.vue';
import {Role, Team} from "@/types/models";

defineProps<{
    team: Team,
    availableRoles: Array<Role>,
    permissions: {
        canAddTeamMembers: boolean,
        canDeleteTeam: boolean,
        canRemoveTeamMembers: boolean,
        canUpdateTeam: boolean,
        canUpdateTeamMembers: boolean,
    },
}>();
</script>

<template>
    <AppLayout title="Team Settings">
        <template #header>
            <h2 class="font-semibold bg-background-light text-lg text-gray-700 leading-tight">
                Team Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 mt-10 space-y-4">
                <div class="bg-background-light-dark rounded-lg p-4">
                    <UpdateTeamNameForm :team="team" :permissions="permissions"/>
                </div>

                <div class="bg-background-light-dark rounded-lg p-4">
                    <TeamMemberManager
                        class="mt-10 sm:mt-0"
                        :team="team"
                        :available-roles="availableRoles"
                        :user-permissions="permissions"
                    />
                </div>

                <template v-if="permissions.canDeleteTeam && ! team.personal_team">
                    <div class="bg-background-light-dark rounded-lg p-4">
                        <DeleteTeamForm class="mt-10 sm:mt-0" :team="team"/>
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
