<?php

namespace App\Support;

use App\Models\User;

class RoleUi
{
    public const SUPER_ADMIN = 'Super Admin';
    public const ADMIN = 'Admin';
    public const AGENT = 'Agent';
    public const AGENT_1 = 'Agent 1';
    public const AGENT_2 = 'Agent 2';
    public const SUPPORT_AGENT = 'Support Agent';
    public const USER = 'User';

    public const FIELD_AGENT_ROLES = [
        self::AGENT,
        self::AGENT_1,
        self::AGENT_2,
        self::SUPPORT_AGENT,
    ];

    public const TICKET_MANAGER_ROLES = [
        self::SUPER_ADMIN,
        self::ADMIN,
    ];

    public const ASSIGNABLE_AGENT_ROLES = [
        self::AGENT_2,
    ];

    public static function isFieldAgent(?User $user): bool
    {
        return $user && $user->hasAnyRole(self::FIELD_AGENT_ROLES);
    }

    public static function canManageAllTickets(?User $user): bool
    {
        return $user && $user->hasAnyRole(self::TICKET_MANAGER_ROLES);
    }

    public static function canAssignTickets(?User $user): bool
    {
        return $user && $user->can('tickets.assign');
    }

    public static function dashboardView(?User $user): string
    {
        if (!$user) {
            return 'agent.dashboard.field-agent';
        }

        if ($user->hasRole(self::SUPER_ADMIN)) {
            return 'agent.dashboard';
        }

        if ($user->hasRole(self::ADMIN)) {
            return 'agent.dashboard.admin';
        }

        // Kepala Bidang (Support Agent) juga pakai agent dashboard
        if ($user->hasRole(self::SUPPORT_AGENT)) {
            return 'agent.dashboard.field-agent';
        }

        return 'agent.dashboard.field-agent';
    }

    public static function portalLabel(?User $user): string
    {
        if (!$user) {
            return 'Portal Agen';
        }

        return match (true) {
            $user->hasRole(self::SUPER_ADMIN) => 'Pusat Komando (Super Admin)',
            $user->hasRole(self::ADMIN) => 'Pusat Penugasan Admin',
            $user->hasRole(self::AGENT_1) => 'Ruang Kerja Agent 1',
            $user->hasRole(self::AGENT_2) => 'Ruang Kerja Agent 2',
            $user->hasRole(self::SUPPORT_AGENT) => 'Ruang Kerja Kepala Bidang',
            $user->hasRole(self::AGENT) => 'Ruang Kerja Analis',
            default => 'Portal Agen',
        };
    }
}
