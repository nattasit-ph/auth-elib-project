<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Session;

class CallbackController extends Controller
{
    // use AuthenticatesUsers;

    // /**
    //  * Where to redirect users after login.
    //  *
    //  * @var string
    //  */
    // protected $redirectTo = RouteServiceProvider::HOME;

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }


    public function callbackLabour(Request $request)
    {
        //defind url
        $auth_url = config('bookdose.app.url');
        $belib_url = config('bookdose.app.belib_url');
        $km_url = config('bookdose.app.km_url');

        // 1. Pre-check parameters
        $type = request()->type;
        $device = request()->device;
        $token = request()->token;
        $word = request()->word;

		$return_data = array('status' => 'error', 'msg' => '');
        if (empty($type) || empty($device) || empty($token)) {
			$return_data['msg'] = 'Missing parameter. Please specify parameter.';
		}
        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        // 2. Set return_url & check token
        if(in_array($device, ['ios', 'android'])){
            Session::put('web_view', 'show');
            switch ($type) {
                case 'km':
                    $return_url = $km_url."/home-m";
                    break;
                case 'elib':
                    $return_url = $belib_url."/home-m";
                    break;
                case 'search_isbn':
                    $return_url = $belib_url."/advanced-search?word=".$word."&search_by=isbn";
                    break;
                default:
                    break;
            }
            $user = User::where('jwt', $token)->firstOrFail();
        }else{
            $return_url = $belib_url;
            if($type == "km"){
                $return_url = $km_url;
            }
            $user = User::where('temp_token', $token)->firstOrFail();
        }

        Auth::login($user);
        // Insert login history
      	$attempt = session('login_attempt', 0);
        $user->device = $device;
        $user->device_id = '';
        parent::insertLoginHistory($user, 'authen-success', $attempt);

        return redirect($return_url);
    }

}
