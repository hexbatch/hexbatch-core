<?php

namespace App\Http\Middleware;

use ForceUTF8\Encoding;
use Illuminate\Foundation\Http\Middleware\TransformsRequest as Middleware;

class ForceUnicode extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    protected array $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    /**
     * All of the registered skip callbacks.
     *
     * @var array
     */
    protected static array $skipCallbacks = [];


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        foreach (static::$skipCallbacks as $callback) {
            if ($callback($request)) {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (in_array($key, $this->except, true) || ! is_string($value)) {
            return $value;
        }

        if(str_starts_with($key,'binary_')) {
            return $value;
        }

        return Encoding::toUTF8($value);

    }

    /**
     * Register a callback that instructs the middleware to be skipped.
     *
     * @param \Closure $callback
     * @return void
     */
    public static function skipWhen(\Closure $callback)
    {
        static::$skipCallbacks[] = $callback;
    }
}
