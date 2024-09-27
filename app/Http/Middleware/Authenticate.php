<?php

namespace App\Http\Middleware;

use App\Models\UserOrg;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $org_slug = $request->org_slug ?? '';
        if ($org_slug != '') {
            $user_org = UserOrg::where('slug', $org_slug)->first();
            $org_slug = $user_org->slug ?? '';
        }
        if (! $request->expectsJson()) {
            if(config('bookdose.sso.login_3rd') == true){
                return config('bookdose.sso.auth_3rd_url');
            }
            return route('login', $org_slug);
        }
    }
}
