<?php

namespace App\Http\Middleware;

use App\Models\UserOrg;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $org_slug = $request->org_slug ?? '';
        $user_org = UserOrg::active()->where('slug', $org_slug)->first();

        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $org_slug = $user_org->slug ?? Auth::User()->org->slug;
                return redirect()->route('home', $org_slug);
            }
        }

        return $next($request);
    }
}
