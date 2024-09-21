<?php

namespace App\Providers;

use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementType;
use App\Models\LocationBound;
use App\Models\Remote;
use App\Models\RemoteActivity;
use App\Models\RemoteStack;
use App\Models\Server;
use App\Models\TimeBound;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Route::model('user', User::class);
        Route::model('user_group', UserGroup::class);
        Route::model('time_bound', TimeBound::class);
        Route::model('location_bound', LocationBound::class);
        Route::model('attribute', Attribute::class);
        Route::model('remote', Remote::class);
        Route::model('remote_activity', RemoteActivity::class);
        Route::model('remote_stack', RemoteStack::class);
        Route::model('element', Element::class);
        Route::model('element_type', ElementType::class);
        Route::model('server', Server::class);

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
