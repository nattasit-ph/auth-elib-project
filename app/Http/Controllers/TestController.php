<?php

namespace App\Http\Controllers;

use App\Models\SiteInfo;
use App\Models\UserOrg;
use Illuminate\Http\Request;
use Auth;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function template_email()
    {
        $result = Auth::user();
        $name = $result->name;
        $is_donot_reply = true;

        return view('front.'.config('bookdose.theme_front').'.mails.user.verify', compact('name', 'is_donot_reply', 'result'));
    }

}
