<?php

namespace App\Http\Controllers;

use App\Models\SiteInfo;
use App\Models\UserOrg;
use Illuminate\Http\Request;

class HomeController extends Controller
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

    public function index()
    {
        return view('home');
    }

    public function ajaxSetLdapConfig(Request $request)
    {
     	if (config('bookdose.login_adldap') === true) {
			$identity = $request->input('username');
			$field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

			if ($field == 'email') {
				config(['bookdose.ldap.locate_users_by' => 'userprincipalname']);
			}
			else {
				config(['bookdose.ldap.locate_users_by' => 'samaccountname']);
			}
		}
		return true;
     	// return response()->json([
     	// 	'identity' => $identity,
     	// 	'field' => $field,
     	// 	'v' => config('bookdose.ldap.locate_users_by'),
     	// ]);
    }

    public function privacy()
    {
    		$site_info = SiteInfo::myOrg(1)
    			->where('meta_key', 'privacy-policy')
    			->where('meta_lang', app()->getLocale())
    			->firstOrFail();
    		$breadcrumbs = [ $site_info->meta_label => '' ];
    		$footer = UserOrg::myOrg()->with(['questionBelib', 'questionKm', 'questionLearnext'])->first();
        	return view('front.'.config('bookdose.theme_front').'.modules.site.privacy', compact('breadcrumbs', 'site_info', 'footer'));
    }

    public function delete_user_privacy()
    {
    		$site_info = SiteInfo::myOrg(1)
    			->where('meta_key', 'delete-user-policy')
    			->where('meta_lang', app()->getLocale())
    			->firstOrFail();
    		$breadcrumbs = [ $site_info->meta_label => '' ];
    		$footer = UserOrg::myOrg()->with(['questionBelib', 'questionKm', 'questionLearnext'])->first();
        	return view('front.'.config('bookdose.theme_front').'.modules.site.privacy', compact('breadcrumbs', 'site_info', 'footer'));
    }

    public function gdpr()
    {
        return view('front.gdpr');
    }

}
