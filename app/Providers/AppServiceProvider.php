<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

use App\Models\Person;
use App\Models\Relationship;
use App\Policies\PersonPolicy;
use App\Observers\PersonObserver;
use App\Observers\RelationshipObserver;
use App\Services\FamilyContext;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /* ---------------------------------
         | 🛡 Политики
         |--------------------------------- */
        Gate::policy(Person::class, PersonPolicy::class);

        /* ---------------------------------
         | 👁 Observers
         |--------------------------------- */
        Person::observe(PersonObserver::class);

        // ⚠️ Если модель Relationship есть — логируем связи
        if (class_exists(Relationship::class)) {
            Relationship::observe(RelationshipObserver::class);
        }

        /* ---------------------------------
         | 🌍 Локаль
         |--------------------------------- */
        App::setLocale('ru');
        Carbon::setLocale('ru');

        /* ---------------------------------
         | 🧩 Blade-директива ролей
         |--------------------------------- */
        Blade::if('familyRole', function (string|array $roles) {
            return FamilyContext::hasRole($roles);
        });
    }
}
