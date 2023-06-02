<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('canAccessUserManagement', static function (User $user) {
            return $user->hasTeamRole($user->currentTeam, 'admin') || $user->ownsTeam($user->currentTeam);
        });
        Gate::define('canAccessTableManagement', static function (User $user) {
            return $user->hasTeamRole($user->currentTeam, 'admin') || $user->hasTeamRole($user->currentTeam, 'editor') || $user->ownsTeam($user->currentTeam);
        });
    }
}
