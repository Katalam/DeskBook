import type {Page} from '@inertiajs/core'
import {PageProps as InertiaPageProps} from '@inertiajs/core';
import {AxiosInstance} from 'axios';
import ziggyRoute, {Config as ZiggyConfig} from 'ziggy-js';
import {PageProps as AppPageProps} from './';
import {decl} from "postcss";

declare global {
    interface Window {
        axios: AxiosInstance;
        Pusher: typeof Pusher;
        Echo: typeof Echo;
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
    interface PageProps extends InertiaPageProps, AppPageProps {
    }
}

declare type canObject = {
    [key: string]: boolean
};

declare type Team = {
    id: number;
};

declare namespace App.Models {
    export interface Team {
        id: number
        user_id: number
        name: string
        personal_team : boolean
        created_at: string
        updated_at: string
    }

    export interface TeamMember {
        team_id: number
        user_id: number
        role: string
        created_at: string
        updated_at: string
    }

    export interface User {
        id: number
        name: string
        email: string
        email_verified_at: string | null
        current_team_id: number | null
        profile_photo_path: string | null
        created_at: string | null
        updated_at: string | null
        two_factor_confirmed_at: string | null
        profile_photo_url: string
    }

    export interface UserSession {
        agent: {
            browser: string
            is_desktop: boolean
            platform: string
        }
        ip_address: string
        is_current_device: boolean
        last_active: string
    }

    export interface TeamInvitation {
        id: number
        team_id: number
        email: string
        role: string
        created_at: string | null
        updated_at: string | null
    }

    export type CRUDPermissions = ('create' | 'read' | 'update' | 'delete')[]

    export interface Role {
        description: string
        key: string
        name: string
        permissions: CRUDPermissions[]
    }

    export interface ApiToken {
        id: number
        name: string
        abilities: CRUDPermissions
        last_used_ago: string | null
        last_used_at: string | null
        created_at: string | null
        updated_at: string | null
        tokeneable_id: number
        tokeneable_type: string
    }

    declare type Room = {
        id: number,
        name: string,
        tables: Array<Table>
    }

    declare type Table = {
        id: number,
        name: string,
        location: string,
        reserved: boolean,
        multiple_bookings: boolean,
        reservations: Array<Reservation>
    }

    declare type Reservation = {
        id: number,
        user: User
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {
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
            user: App.Models.User & {
                all_teams: Array<App.Models.Team>
                current_team: App.Models.Team
                two_factor_enabled: boolean
                membership?: TeamMember
            },
        }
        can: canObject;
    }
}


