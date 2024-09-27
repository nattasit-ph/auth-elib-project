<?php

namespace App\Http\Middleware;

use App;
use Config;
use Session;
use Closure;
use Illuminate\Http\Request;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = Session::get('locale', Config::get('app.locale'));
        App::setLocale($lang);
        return $next($request);
    }
}
