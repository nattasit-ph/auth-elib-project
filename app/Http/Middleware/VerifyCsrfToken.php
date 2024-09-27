<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Auth;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'login'
    ];

    public function handle($request, Closure $next)
    {
        if(!Auth::check() && $request->route()->named('logout')) {
            $this->except[] = ( !is_blank($request->org_slug ?? '') ? route('logout', $request->org_slug) : route('logout') );
        }

        return parent::handle($request, $next);
    }
}
