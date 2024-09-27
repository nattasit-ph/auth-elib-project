<?php

namespace App\Http\Controllers\Back\Migration;

use DB;
use Auth;
use Session;
use App\Models\User;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class MigrationController extends BackController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            parent::getSiteConfig();
                
            $this->user = Auth::user();
            if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib', 'Super Admin Learnext', 'Admin Learnext', 'Super Admin KM', 'Admin KM'])) {
                return $next($request);
            } else {
                return redirect()->route('home');
            }
        });
    }

    public function craUserImg(Request $request)
	{
        // $dir = storage_path('app/public/image_user');
        $folder_import = "image_user";
        $folder_target = "avatars";
        $dir = storage::path($folder_import);
        // Open a directory, and read its contents
        if (is_dir($dir)){
            if ($dh = opendir($dir)){
                $i=0;
                while (($file = readdir($dh)) !== false){
                
                    $filename = $file;
                    $filename = explode(".", $file);
                    $filename = current($filename);
                    if(!empty($filename)){
                        $i++;
                        
                        $user = User::where('member_id', $filename)->first();
                       
                        if($user){
                            try {
                                Storage::move($folder_import.'/'.$file, $folder_target."/".$file);
                                User::where('member_id', $user->member_id)->update(['avatar_path' => 'avatars/'.$file]);
                                echo '<div style="background-color:#0B5B03;color:white">';
                                echo '['.$i.'][File name: '.$file.' => path : '.$folder_target."/".$file.'] move file success.';
                                echo '</div>';
                            } catch (\Throwable $e) {
                                echo '<div style="background-color:#951407;color:white">';
                                echo '['.$i.'][Member_id: '.$user->memeber_id.'] => '. $e->getMessage();
                                echo '</div>';
                            }
                        }
                        //  dd($filename, $file, $user, $folder_import, $folder_target);
                    }

                }
                closedir($dh);
            }
        }
	}

}