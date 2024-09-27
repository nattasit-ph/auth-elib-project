<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function __construct()
	{
		$this->middleware(function ($request, $next) {
            parent::change_config_for_web_view();
			return $next($request);
		});
	}
}
