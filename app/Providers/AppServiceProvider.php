<?php

namespace App\Providers;

use App\Sys\Collections\SystemTypes;
use App\Sys\Res\Types\BaseType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
        Model::automaticallyEagerLoadRelationships();

        /** @var BaseType $class $class */
        foreach (SystemTypes::loadClasses() as $class) {
            $class::registerAction();
        }
    }
}
