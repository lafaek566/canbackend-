<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;          /*(Tambahan)*/

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',    /*(Tambahan)*/
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
//        Passport::routes();     /*(Tambahan)*/
        Passport::routes(null, ['middleware' => [ \Barryvdh\Cors\HandleCors::class ]]);   /*(Tambahan)*/
    }
}
