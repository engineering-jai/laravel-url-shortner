<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ShortUrl;
use App\Models\Invitation;
use App\Policies\ShortUrlPolicy;
use App\Policies\InvitationPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ShortUrl::class, ShortUrlPolicy::class);
        Gate::policy(Invitation::class, InvitationPolicy::class);
    }
}
