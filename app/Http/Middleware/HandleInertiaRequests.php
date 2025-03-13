<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
                'previous' => url()->previous(),
                'routes' => $this->routes($request),
            ],
        ];
    }

    protected function routes(Request $request): array
    {
        $all = (new Ziggy)->toArray()['routes'];
        $keys = array_keys($all);
        $routes = [];
        $user = $request->user();
        $public = collect($keys)->filter(static function ($key) {
            return str_contains($key, 'public');
        })->mapWithKeys(static function ($key) use ($all) {
            return [$key => $all[$key]];
        })->toArray();

        if ($user) {
            $user_roles = $user->roles->pluck('name')->toArray();

            $routes = collect($keys)->filter(static function ($key) use ($user_roles) {

                $hasRole = collect($user_roles)->filter(static function ($role) use ($key) {
                    return str_contains($key, $role);
                });

                return $hasRole->isNotEmpty();
            })->mapWithKeys(static function ($key) use ($all) {
                return [$key => $all[$key]];
            })->toArray();
        }


        return array_merge($public, $routes);
    }

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }
}
