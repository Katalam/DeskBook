export interface Team {
    id: number
    user_id: number
    name: string
    personal_team: boolean
    created_at: string
    updated_at: string
    owner: User,
    personio_client_id: string | null,
    personio_client_secret: string | null,
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

export interface Room {
    id: number,
    name: string,
    tables: Array<Table>,
    is_outside?: boolean,
}

export interface Table {
    id: number,
    name: string,
    location: string,
    reserved: boolean,
    multiple_bookings: boolean,
    reservations: Array<Reservation>,
    room_id?: number,
    room_name?: string,
    is_favorite?: boolean,
    time_off_type_id?: number,
}

export interface Reservation {
    id: number,
    date: string,
    user: User
    table?: Table,
}

export interface TeamPermissions {
    canAddTeamMembers: boolean,
    canDeleteTeam: boolean,
    canRemoveTeamMembers: boolean,
    canUpdateTeam: boolean,
    canUpdateTeamMembers: boolean,
}

export interface Feature {
    id: number,
    name: string,
}
