import {PageProps as InertiaPageProps} from '@inertiajs/core';
import {AxiosInstance} from 'axios';
import ziggyRoute, {Config as ZiggyConfig} from 'ziggy-js';
// @ts-ignore
import {Team, TeamMember, User} from "@/types/models";
import type Echo from "laravel-echo";
import Pusher from "pusher-js";

declare global {
    interface Window {
        axios: AxiosInstance;
        Pusher: typeof Pusher;
        Echo: Echo;
    }

    let route: typeof ziggyRoute;
    let Ziggy: ZiggyConfig;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps {
    }
}

declare type canObject = {
    [key: string]: boolean
};

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps {
        jetstream: {
            canCreateTeams: boolean;
            canManageTwoFactorAuthentication: boolean;
            canUpdatePassword: boolean;
            canUpdateProfileInformation: boolean;
            hasEmailVerification: boolean
            flash: {
                bannerStyle?: 'success' | 'danger',
                banner?: string,
                token?: string
            }
            hasAccountDeletionFeatures: boolean;
            hasApiFeatures: boolean;
            hasTeamFeatures: boolean;
            hasTermsAndPrivacyPolicyFeature: boolean;
            managesProfilePhotos: boolean;
        },
        auth: {
            user: User & {
                all_teams: Array<Team>
                current_team: Team
                two_factor_enabled: boolean
                membership?: TeamMember
            },
        }
        can: canObject;
    }
}


