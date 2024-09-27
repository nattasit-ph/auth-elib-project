<?php
use Illuminate\Support\Str;
use App\Models\UserOrg;
use App\Models\Form;
use App\Models\RewardRedemptionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

function base64UrlEncode($text)
{
    return str_replace(
        ['+', '/', '='],
        ['-', '_', ''],
        base64_encode($text)
    );
}

function isSuperAdmin($user = '')
{
	if (empty($user)) $user = Auth::user();
	if ($user->hasAnyRole(['Super Admin Belib'])) {
		return true;
	}
	return false;
}
function isAdminOrHigher($user = '')
{
	if (empty($user)) $user = Auth::user();
	if ($user->hasAnyRole(['Super Admin Belib', 'Admin Belib'])) {
		return true;
	}
	return false;
}

function isBookdose()
{
	$arr_email = explode("@", Auth::user()->email);
	if (Auth::user()->org->is_bd == 1 || Auth::user()->is_bd == 1)
		return true;
	else
		return false;
}

function accessBackend($user = '')
{
	if (empty($user)) $user = Auth::user();
	if ($user->hasAnyRole(['Super Admin', 'Bookdose Admin', 'Partner Admin', 'Tester', 'Admin', 'Staff',
							'Super Admin Belib', 'Partner Admin Belib', 'Tester Belib', 'Admin Belib', 'Staff Belib'])) {
		return true;
	}
	return false;
}

function getLogo($css_class='img-fluid', $inline_style='max-height: 70px;')
{
	if (!empty(config('bookdose.app.logo'))) {
		return '<img alt="Logo" src="'.asset(config('bookdose.app.folder').'/'.config('bookdose.app.logo')).'" class="'.$css_class.'" style="'.$inline_style.'"/>';
	}
	else {
		return '<img alt="Logo" src="'.asset('media/img/logo/logo_LearnNext.png').'" class="'.$css_class.'" style="'.$inline_style.'"/>';
	}
}

function getOrgLogoPath(){
	$url = UserOrg::MyOrg()->first();
	if (is_null($url))
		return $url;

    if (Str::contains($url->logo_path, ['http://', 'https://'])) {
        return $url->logo_path;
    }
    else {
        return (Storage::disk('s3')->exists($url->logo_path) ? Storage::disk('s3')->url($url->logo_path) : Storage::url($url->logo_path));
    }
}

function getOrgLogo($org_slug='') {
	$url = !is_blank($org_slug) ? UserOrg::ofSlug( $org_slug )->first() : UserOrg::where('slug', config('bookdose.default.home_slug') )->first();

    if (is_null($url)) {
        $url = UserOrg::MyOrg()->first();
    }
	if (is_null($url)) {
        $url = UserOrg::ofSlug( config('bookdose.default.user_org') )->first();
    }
    if (is_null($url))
		return $url;

    if (Str::contains($url->logo_path, ['http://', 'https://'])) {
        return $url->logo_path;
    }
    else {
        return (Storage::disk('s3')->exists($url->logo_path) ? Storage::disk('s3')->url($url->logo_path) : Storage::url($url->logo_path));
    }
}

function getLoginCoverImage($cover_image_path)
{
	$default_image = !empty(config('bookdose.default_image.banner_login')) ? asset(config('bookdose.app.project').'/'.config('bookdose.default_image.banner_login')) : asset('images/login_bg.jpg');
	if (!empty($cover_image_path)) {
		if (!(Str::contains($cover_image_path, ['http://', 'https://']))) {
			if (!empty($cover_image_path) && file_exists('storage/'.$cover_image_path)) {
				$default_image = Storage::url($cover_image_path);
			}
			else {
				// $default_image = config('bookdose.app.main_product_redirect').'/storage/'.$cover_image_path;
                $default_image = (Storage::disk('s3')->exists($cover_image_path) ? Storage::disk('s3')->url($cover_image_path) : Storage::url($cover_image_path));
			}
		}
		else {
			$default_image = $cover_image_path;
		}
	}
	return $default_image;
}

function getRegisterCoverImage($cover_image_path)
{

	$default_image = !empty(config('bookdose.default_image.banner_register')) ? asset(config('bookdose.app.folder').'/'.config('bookdose.default_image.banner_register')) : asset('images/register_bg.jpg');
	if (!empty($cover_image_path)) {
		if (!(Str::contains($cover_image_path, ['http://', 'https://']))) {
			if (!empty($cover_image_path) && file_exists('storage/'.$cover_image_path)) {
				$default_image = Storage::url($cover_image_path);
			}
			else {
				$default_image = config('bookdose.app.main_product_redirect').'/storage/'.$cover_image_path;
			}
		}
		else {
			$default_image = $cover_image_path;
		}
	}
	return $default_image;
}

function getAvatarImage($image_path, $default_image = '', $only_path = false, $css_class = 'img-fluid rounded-circle', $inline_style = 'width:50px; height:50px;')
{
	if (empty($default_image)) {
		$default_image = asset('images/default_avatar.png');
	}

	if (!empty($image_path) && in_array(substr($image_path, -3, 3), array('jpg', 'jpeg', 'png', 'gif'))) {
		if (!(Str::contains($image_path, ['http://', 'https://']))) {
			$image_path = config('bookdose.sso.auth_url') . '/storage/' . $image_path . '?z=' . Str::random(40);
		}
	}

	if ($only_path)
		return $image_path;
	else
		return '<img src="' . $image_path . '" class="' . $css_class . '" style="' . $inline_style . '" onerror="this.src=\'' . $default_image . '\'"/>';
}

function _getAvatar($user, $css_class='rounded-circle img-fluid', $inline_style='width: 160px; height: 160px;')
{
	if (!empty($user->avatar_path) && in_array(strtolower(substr($user->avatar_path, -3, 3)), array('jpg', 'jpeg', 'png', 'gif') ) && file_exists('storage/'.$user->avatar_path)) {
		if (!Str::contains($user->avatar_path, ['http://', 'https://'])) {
			return '<img src="'.asset('storage/'.$user->avatar_path).'" alt="avatar" class="'.$css_class.'" style="'.$inline_style.'">';
		}
		else {
			return '<img src="'.$user->avatar_path.'" alt="avatar" class="'.$css_class.'" style="'.$inline_style.'">';
		}
	}
	else {
		return '<img src="'.url('media/img/default-avatar.png').'" alt="avatar" class="'.$css_class.'" style="'.$inline_style.'">';
	}
}

function getCoverImage($cover_image_path, $default_name='placeholder', $only_path = false, $css_class = '', $inline_style = '')
{
	$default_image = asset('' . config('bookdose.app.folder') . '/images/placeholder/default-' . $default_name . '.png');
	$image_path = $cover_image_path;

	if (!empty($cover_image_path)) {
		if (!(Str::contains($cover_image_path, ['http://', 'https://']))) {
			if (!empty($cover_image_path) && file_exists('storage/' . $cover_image_path)) {
				$image_path = Storage::url($cover_image_path);
			}
		}
	}

	if ($only_path)
		return $image_path;
	else
		return '<img src="' . $image_path . '" class="' . $css_class . '" style="' . $inline_style . '" onerror="this.src=\''.$default_image.'\'" />';
}

function getCoverImageFromSite($site_url, $cover_image_path, $default_name='placeholder', $only_path = false, $css_class = '', $inline_style = '')
{
	$default_image = asset('' . config('bookdose.app.folder') . '/images/placeholder/default-' . $default_name . '.png');
	$image_path = $cover_image_path;

	if (!empty($cover_image_path)) {
		if (!(Str::contains($cover_image_path, ['http://', 'https://']))) {
			$image_path = $site_url.'/storage/'.$cover_image_path;
		}
	}

	if ($only_path)
		return $image_path;
	else
		return '<img src="' . $image_path . '" class="' . $css_class . '" style="' . $inline_style . '" onerror="this.src=\''.$default_image.'\'" />';
}


function langNumber($number)
{
	$numthai = array("๑", "๒", "๓", "๔", "๕", "๖", "๗", "๘", "๙", "๐");
	$numarabic = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
	switch (app()->getLocale()) {
		case 'en':
			return str_replace($numthai, $numarabic, $number);
		case 'th':
			return str_replace($numarabic, $numthai, $number);
	}
}

function getFirstQuestionnaire(){
	$questionnaire = Form::Active()->select('slug')->orderBy('updated_at', 'desc')->first();
	return $questionnaire->slug ?? '';
}

function pushNotification($device, $device_list, $message, $type_push="", $id_push="") {
	$fields = [];
	$fields['appname'] = config('bookdose.app.folder');
	$fields['device'] = $device;
	$fields['device_list'] = $device_list;
	$fields['msg'] = $message;
	$fields['type_push'] = $type_push;
	$fields['id_push'] = $id_push;

	$push_msg = Http::withoutVerifying()
					->withHeaders(['Content-Type' => 'application/json'])
					->withOptions(["verify"=>false])
					->post(config('bookdose.notification.pushmsg_url'), $fields);

    if ($push_msg === FALSE) {
        return false;
    }

    return true;
}


function pre($arr,$var_dump=0) {
	echo "<pre>";
	if($var_dump==0){
		print_r($arr);
	}else{
		var_dump($arr);
	}
	echo "</pre>";
}

function getDayFooter($start=0,$end=0) {
	$org_info = UserOrg::MyOrg()->first();

	// filter day
	$w_day = $org_info->working_day;
	$json  = json_decode($w_day);
	$s     = $json->start ?? 0;
	$e     = $json->end ?? 0;

	if(app()->getLocale() == "en"){
        $days = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];
    }else{
        $days = [
            'วันอาทิตย์',
            'วันจันทร์',
            'วันอังคาร',
            'วันพุธ',
            'วันพฤหัสบดี',
            'วันศุกร์',
            'วันเสาร์'
        ];
    }

	$working_day = $days[$s]." - ".$days[$e];
	return $working_day;
}

/** check about data **/
function is_blank($obj)
{
	if (is_number($obj)) return false;
	else if (is_null($obj) || (is_string($obj) && trim($obj) == "") || empty($obj)) return true;
	else return false;
}
function is_number($obj)
{
	if (is_numeric($obj) || is_float($obj) || is_int($obj)) return true;
	else return false;
}
function is_number_no_zero($obj)
{
	if ((is_number($obj) && $obj > 0)) return true;
	else return false;
}
function countRedemption()
{
	$rs = RewardRedemptionHistory::where('is_delivered',0)->count();
	return $rs;
}
