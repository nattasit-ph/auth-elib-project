<?php

namespace App\Http\Controllers\Back\Site;

use DB;
use Auth;
use Session;

use App\Models\SiteInfo;
use App\Models\UserOrg;
use App\Http\Controllers\Back\BackController;
use App\Models\Policys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteInfoController extends BackController
{
	public function __construct()
 	{
 		$this->middleware(function ($request, $next) {
        	parent::getSiteConfig();

        	$this->user = Auth::user();
    		if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib', 'Super Admin Learnext', 'Admin Learnext', 'Super Admin KM', 'Admin KM'])) {
    			return $next($request);
 			}
 			else {
 				return redirect()->route('home');
 			}
     	});
 	}

	public function index()
	{
        //
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function editOrgInfo(Request $request)
    {
        $org_slug = Auth::user()->org->slug;

        if(app()->getLocale() == "en"){
            $days = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
        }else{
            $days = [ 'วันอาทิตย์', 'วันจันทร์', 'วันอังคาร', 'วันพุธ', 'วันพฤหัสบดี', 'วันศุกร์', 'วันเสาร์' ];
        }

        $org_info = UserOrg::findOrFail(Auth::user()->user_org_id);
        $w_day = json_decode($org_info['working_day']);
        $day_start = $w_day->start ?? '';
        $day_end = $w_day->end ?? '';
    	return view('back.'.config('bookdose.theme_back').'.modules.site.form', compact('org_slug', 'org_info','days','day_start','day_end'));
    }

    public function updateOrgInfo(Request $request)
    {
		$org_slug = Auth::user()->org->slug;

    	$org_info = UserOrg::findOrFail(Auth::user()->user_org_id);
 		$validatedData = $request->validate([
		    'name_en' => 'required|max:255',
		    'name_th' => 'required|max:255',
		    'address_en' => 'nullable',
		    'address_th' => 'nullable',
		    'phone' => 'nullable',
		    'fax' => 'nullable',
		    'contact_email' => 'required|email|max:255',
            'working_time' => 'nullable',
		]);
		$validatedData['updated_by'] = Auth::user()->id;
        $validatedData['working_day'] = json_encode(array('start' => $request->days_s ?? '', 'end' => $request->days_e ?? '')) ;

        $data_org_info = [
            'name_en' => $request->name_en ?? null,
            'name_th' => $request->name_th ?? null,
            'data_contact' => [
                // Main Contact
                'address_en' => $request->address_en ?? null,
                'address_th' => $request->address_th ?? null,
                'phone' => $request->phone ?? null,
                'fax' => $request->fax ?? null,
                'contact_email' => $request->contact_email ?? null,
                // Social Media
                'line' => $request->line ?? null,
                'facebook' => $request->facebook ?? null,
                'twitter' => $request->twitter ?? null,
                'youtube' => $request->youtube ?? null,
                'instagram' => $request->instagram ?? null,
                'google_map' => $request->google_map ?? null,
                'website' => $request->website ?? null,
            ],
        ];

    	if ($request->logo_path)
    	{
    		// Storage::delete($request->logo_path);
            if (Storage::disk('s3')->exists($org_info->logo_path)) {
                Storage::disk('s3')->delete($org_info->logo_path);
            }
            if (Storage::exists($org_info->logo_path)) {
                Storage::delete($org_info->logo_path);
            }
    		// Storage::delete($request->logo_path);

            $upload_dir = join('/', [config('bookdose.app.store_prefix'), 'logos', $org_info->id]);
    		// $path = $request->logo_path->store('logos');
            $path = $request->logo_path->store( $upload_dir );
            if ($path) {
                try {
                    // Upload the local file to S3 with specific ACL (e.g., 'private', 'public-read')
                    $path = Storage::disk('s3')->putFileAs( pathinfo($path)['dirname'],  getcwd().Storage::url($path), basename($path), ['ACL' => 'public-read',]);
                    Storage::delete($path);
                } catch (\Exception $e) {
                    // return "File upload failed: " . $e->getMessage();
                }
                Storage::delete($request->logo_path);
            }
    		$data_org_info['logo_path'] = $path;
    	}
  		UserOrg::where('id', Auth::user()->user_org_id)->update($data_org_info);

      //--- Start log ---//
    	$log = collect([ (object)[
    		'module' => 'Org Info',
    		'severity' => 'Info',
    		'title' => 'Update',
    		'desc' => '[Succeeded] - '.$org_info->name_en,
    	]])->first();
    	parent::Log($log);
        //--- End log ---//
    	return redirect()->route('admin.site.editOrgInfo', $org_slug)->with('success', 'Organization info is successfully updated.');
    }

    public function editPrivacyPolicy()
    {
    	$site_info = SiteInfo::myOrg()->where('meta_key', 'privacy-policy')->get();
    	if ($site_info->count() <= 0) {
    		SiteInfo::create([
    			'meta_lang' => 'th',
    			'meta_label' => 'นโยบายความเป็นส่วนตัว',
    			'meta_key' => 'privacy-policy',
    			'meta_input_type' => 'textarea',
    			'created_by' => Auth::user()->id,
    			'user_org_id' => Auth::user()->user_org_id,
    		]);
    		SiteInfo::create([
    			'meta_lang' => 'en',
    			'meta_label' => 'Privacy Policy',
    			'meta_key' => 'privacy-policy',
    			'meta_input_type' => 'textarea',
    			'created_by' => Auth::user()->id,
    			'user_org_id' => Auth::user()->user_org_id,
    		]);
    		$site_info = SiteInfo::myOrg()->where('meta_key', 'privacy-policy')->get();
    	}
    	return view('back.'.config('bookdose.theme_back').'.modules.privacy_policy.form', compact('site_info'));
    }

    public function updatePrivacyPolicy(Request $request)
    {
    	foreach ($request->meta_key as $k=>$meta_key) {
    		$meta_lang = $request->meta_lang[$k];
    		$meta_value = $request->{$meta_key.'_'.$meta_lang};
    		$data['meta_value'] = trim($meta_value);
    		$data['updated_by'] = Auth::user()->id;
    		SiteInfo::myOrg()->where([
    				'meta_key' => $meta_key,
    				'meta_lang' => $meta_lang,
    			])->update($data);
    	}
        //--- Start log ---//
    	$log = collect([ (object)[
    		'module' => 'Privacy Policy',
    		'severity' => 'Info',
    		'title' => 'Update',
    		'desc' => '[Succeeded]',
    	]])->first();
    	parent::Log($log);
        //--- End log ---//
    	return redirect()->route('admin.site.editPrivacyPolicy', 1)->with('success', 'Privacy policy is successfully updated.');
    }

	public function editDeleteUserPolicy()
    {
    	$site_info = SiteInfo::myOrg()->where('meta_key', 'delete-user-policy')->get();
    	if ($site_info->count() <= 0) {
    		SiteInfo::create([
    			'meta_lang' => 'th',
    			'meta_label' => 'นโยบายการลบผู้ใช้งาน',
    			'meta_key' => 'delete-user-policy',
    			'meta_input_type' => 'textarea',
    			'created_by' => Auth::user()->id,
    			'user_org_id' => Auth::user()->user_org_id,
    		]);
    		SiteInfo::create([
    			'meta_lang' => 'en',
    			'meta_label' => 'Delete User Policy',
    			'meta_key' => 'delete-user-policy',
    			'meta_input_type' => 'textarea',
    			'created_by' => Auth::user()->id,
    			'user_org_id' => Auth::user()->user_org_id,
    		]);
    		$site_info = SiteInfo::myOrg()->where('meta_key', 'delete-user-policy')->get();
    	}
    	return view('back.'.config('bookdose.theme_back').'.modules.delete_user_policy.form', compact('site_info'));
    }

	public function updateDeleteUserPolicy(Request $request)
    {
    	foreach ($request->meta_key as $k=>$meta_key) {
    		$meta_lang = $request->meta_lang[$k];
    		$meta_value = $request->{$meta_key.'_'.$meta_lang};
    		$data['meta_value'] = trim($meta_value);
    		$data['updated_by'] = Auth::user()->id;
    		SiteInfo::myOrg()->where([
    				'meta_key' => $meta_key,
    				'meta_lang' => $meta_lang,
    			])->update($data);
    	}
        //--- Start log ---//
    	$log = collect([ (object)[
    		'module' => 'Delete User Policy',
    		'severity' => 'Info',
    		'title' => 'Update',
    		'desc' => '[Succeeded]',
    	]])->first();
    	parent::Log($log);
        //--- End log ---//
    	return redirect()->route('admin.site.editDeleteUserPolicy', 1)->with('success', 'Detele user policy is successfully updated.');
    }

	public function GoogleAnalytics()
    {
        $org_slug = Auth::user()->org->slug;

    	$site_info = SiteInfo::myOrg()->where('meta_key', 'google-analytics')->get();
    	if ($site_info->count() <= 0) {
    		SiteInfo::create([
    			'meta_lang' => 'th',
    			'meta_label' => 'Google Analytics',
    			'meta_key' => 'google-analytics',
    			'meta_input_type' => 'text',
    			'created_by' => Auth::user()->id,
    			'user_org_id' => Auth::user()->user_org_id,
    		]);
    		$site_info = SiteInfo::myOrg()->where('meta_key', 'google-analytics')->get();
    	}
    	return view('back.'.config('bookdose.theme_back').'.modules.google_analytics.form', compact('org_slug', 'site_info'));
    }

	public function updateGoogleAnalytics(Request $request)
    {
        $org_slug = Auth::user()->org->slug;

    	foreach ($request->meta_key as $k=>$meta_key) {
    		$meta_lang = $request->meta_lang[$k];
    		$meta_value = $request->{$meta_key.'_'.$meta_lang};
    		$data['meta_value'] = trim($meta_value);
    		$data['updated_by'] = Auth::user()->id;
    		SiteInfo::myOrg()->where([
    				'meta_key' => $meta_key,
    				'meta_lang' => $meta_lang,
    			])->update($data);
    	}
        //--- Start log ---//
    	$log = collect([ (object)[
    		'module' => 'Google Analytics',
    		'severity' => 'Info',
    		'title' => 'Update',
    		'desc' => '[Succeeded]',
    	]])->first();
    	parent::Log($log);
        //--- End log ---//
    	return redirect()->route('admin.site.GoogleAnalytics', $org_slug)->with('success', 'Google Analytics is successfully updated.');
    }

    ############ Consent #####################

    public function consent()
    {
        return view('back.'.config('bookdose.theme_back').'.modules.policy.list_consent');
    }
    public function consentAdd(){
     // echo "add_consent";
        return view('back.'.config('bookdose.theme_back').'.modules.policy.consent_add');
    }
    public function consentEdit($id){
        $consent_result = DB::table('consent_control')
         ->select('*')
         ->where('id', '=', $id)
         ->get();
        return view('back.'.config('bookdose.theme_back').'.modules.policy.consent_edit',compact('consent_result'));
    }


    public function consentLog(){

        $count_agree = DB::table('consent_user')->where('status',1)->count();
        $count_not_agree = DB::table('consent_user')->where('status',0)->count();

        if(empty($count_agree) || $count_agree < 1){
            $count_agree = 0;
        }
        if(empty($count_not_agree) || $count_not_agree < 1){
         $count_not_agree = 0;
        }

        $sum_agree = $count_agree+$count_not_agree;

        if($sum_agree < 1){
            $sum_agree = 1;
        }

        $percen_agree = round(($count_agree/$sum_agree)*100,2);
        $percen_not_agree = round(($count_not_agree/$sum_agree)*100,2);

        return view('back.'.config('bookdose.theme_back').'.modules.policy.consent_log',compact('count_agree','count_not_agree','percen_agree','percen_not_agree'));

   }

    public function consentSave(Request $request){

        $re_consent = $request->post('re_consent');
        $detail_en  = $request->post('detail_en');
        $detail_th  = $request->post('detail_th');

        $data_insert = array(
            're_consent' =>$re_consent,
            'detail_th'  =>$detail_th,
            'detail_en'  =>$detail_en,
            'created_at' =>date("Y-m-d H:i:s"),
            'updated_at' =>date("Y-m-d H:i:s"),
        );
        #insert
        $consent_id = DB::table('consent_control')->insertGetId($data_insert);

        #update
        $data_update = array(
            'version' =>$consent_id,
        );
        DB::table('consent_control')->where('id',$consent_id)->update($data_update);

        return redirect()->route('admin.site.consent.add')->with('success', 'Consent is successfully saved.');

    }
    public function consentUpdate(Request $request){

        $id         = $request->post('consent_id');
        $re_consent = $request->post('re_consent');
        $detail_en  = $request->post('detail_en');
        $detail_th  = $request->post('detail_th');

        #update
        $data_update = array(
            're_consent' =>$re_consent,
            'detail_th'  =>$detail_th,
            'detail_en'  =>$detail_en,
            'updated_at' =>date("Y-m-d H:i:s"),
        );
        DB::table('consent_control')->where('id',$id)->update($data_update);

        return redirect()->route('admin.site.consent.edit',compact('id'))->with('success', 'Consent is successfully saved.');

    }



    public function getConsentUser(Request $request)
    {
        $start_date = $request->post('start_date');
        $end_date   = $request->post('end_date');

        $query = DB::table('consent_user');
        $query->select('*');
        if($start_date != "" && $end_date != ""){
            $start_date_str = date("Y-m-d", strtotime(str_replace('/', '-', $start_date)))." 00:00:00";
            $end_date_str   = date("Y-m-d", strtotime(str_replace('/', '-', $end_date)))." 23:59:59";
            $query->whereBetween('created_at',[$start_date_str, $end_date_str]);
        }
        $consent = $query->get();

        $consent_arr = array();
        $consent_arr['data'] = array();
        if(!empty($consent)):
            foreach($consent as $row):
                $id         = $row->id;
                $device     = $row->device;
                $version    = $row->version;
                $status     = $row->status;
                $user_id    = $row->user_id;
                $created_at = $row->created_at;

                if($status == 1){
                $status_name = "&nbsp; ให้การยินยอม";
                $check_status = '<a class="btn btn-success btn-sm-icon text-white status_'.$id.'" status_val="'.$status.'" onclick="setConsentStatus('.$id.')"><i class="fa fa-check"></i></a>';
                }else{
                $status_name = "&nbsp; ไม่ให้/ถอนความยินยอม";
                $check_status = '<a class="btn btn-danger btn-sm-icon text-white status_'.$id.'" status_val="'.$status.'" onclick="setConsentStatus('.$id.')"><i class="fa fa-ban"></i></a>';
                }

                $user_result = DB::table('users')->select('*')->where('id', '=', $user_id)->get();
                $first_name_th = "";
                if(count($user_result)>0){
                $first_name_th = $user_result[0]->name;
                }

                $consent_arr['data'][] = array(
                    $id,
                    $first_name_th." (".$user_id.")",
                    $device,
                    "ยินยอมใน Version : ".$version,
                    $check_status." ".$status_name,
                    $created_at,
                );
            endforeach;
        endif;
        $json = json_encode($consent_arr);
        echo $json;
    }
    public function getConsentControl()
    {
        $consent = DB::table('consent_control')->select('*')->get();
        $consent_arr = array();
        $consent_arr['data'] = array();
        if(!empty($consent)):
            foreach($consent as $row):
                $id         = $row->id;
                $detail_th  = $row->detail_th;
                $detail_en  = $row->detail_en;
                $re_consent = $row->re_consent;
                $version    = $row->version;
                $created_at = $row->created_at;

                $detail_th_str = mb_substr(strip_tags($detail_th), 0, 80, "utf-8");
                $detail_en_str = mb_substr(strip_tags($detail_en), 0, 80, "utf-8");

                $link = route('admin.site.consent.edit',$id);

                $edit = '<span class="dropdown">
                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
                    <i class="la la-ellipsis-h"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-33px, 29px, 0px);">
                    <a class="dropdown-item" href="'.$link.'"><i class="la la-edit mt-icon-down-5"></i> Edit</a></div>
                    </span>';

                $consent_arr['data'][] = array(
                    "V.".$version,
                    $detail_th_str,
                    $detail_en_str,
                    "แจ้งเตือนทุก ".$re_consent." เดือน",
                    $created_at,
                    $edit,
                );
            endforeach;
        endif;
        $json = json_encode($consent_arr);
        echo $json;
    }


    public function updateStatusConsentUser(Request $request){

        $id     = $request->post('id');
        $status = $request->post('status');
        #update
        $data_update = array(
            'status'     =>$status,
            'updated_at' =>date("Y-m-d H:i:s"),
        );
        DB::table('consent_user')->where('id',$id)->update($data_update);
    }







    ############ Policy #####################

    public function addCookie()
    {
        $org_slug = Auth::user()->org->slug;

        $cookie = Policys::Cookie()->first();
        return view('back.'.config('bookdose.theme_back').'.modules.policy.add_cookie', compact('org_slug', 'cookie'));
    }
    public function addPolicy()
    {
        $org_slug = Auth::user()->org->slug;

        $policy = Policys::Privacy()->first();
        return view('back.'.config('bookdose.theme_back').'.modules.policy.add_policy', compact('org_slug', 'policy'));
    }

    public function addTerms()
    {
        $org_slug = Auth::user()->org->slug;

        $terms = Policys::Terms()->first();
        return view('back.'.config('bookdose.theme_back').'.modules.policy.add_terms', compact('org_slug', 'terms'));
    }

    public function savePolicyAndTerms(Request $request)
    {
        $org_slug = Auth::user()->org->slug;

		$validatedData = $request->validate([
			'check_type' => 'required|numeric',
			'detail_th' => 'nullable',
			'detail_en' => 'nullable',
		]);

        // $check_result = $request->post('check_result') ?? '';
        $check_type = $request->post('check_type') ?? '';
        $detail_th = $request->post('detail_th') ?? '';
        $detail_en = $request->post('detail_en') ?? '';

        switch ($check_type) {
            case 1: $cookie_name = "Privacy Policy"; break;
            case 2: $cookie_name = "Terms and Conditions"; break;
            case 3: $cookie_name = "Cookie Policy"; break;
        }

        $policy = Policys::where('type', $check_type)->first();
        $data_policy = [
            'name'       => $cookie_name,
            'detail_th'  => $detail_th,
            'detail_en'  => $detail_en,
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        if ($policy) {
            Policys::where('type', $check_type)->update($data_policy);
        }
        else {
            $data_policy['type'] = $check_type;
            $data_policy['created_at'] = date("Y-m-d H:i:s");
            Policys::create($data_policy);
        }

        if ($check_type == 1) {
            return redirect()->route('admin.site.addPolicy', $org_slug)->with('success', 'Privacy Policy is successfully saved.');
        }
        else if ($check_type == 2) {
            return redirect()->route('admin.site.addTerms', $org_slug)->with('success', 'Terms and conditions is successfully saved.');
        }
        else {
            return redirect()->route('admin.site.addCookie', $org_slug)->with('success', 'Cookie Policy is successfully saved.');
        }

    }





}
