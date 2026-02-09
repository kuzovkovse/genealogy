<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Person;
use App\Models\Couple;
use App\Policies\PersonPolicy;
use App\Policies\CouplePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Person::class => PersonPolicy::class,
        Couple::class => CouplePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies(); // 🔥 ОБЯЗАТЕЛЬНО
    }
}
