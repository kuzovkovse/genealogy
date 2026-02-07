<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use App\Models\Person;
use App\Policies\PersonPolicy;
use App\Services\FamilyContext;

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
        // 🛡 Политики
        Gate::policy(Person::class, PersonPolicy::class);

        // 🧩 Blade-директива для ролей семьи
        Blade::if('familyRole', function (string|array $roles) {
            return FamilyContext::hasRole($roles);
        });
    }
}
