<?php

namespace App\Http\Controllers\Back\Room;

use DB;
use Auth;
use Session;
use App\Http\Controllers\Back\BackController;
use App\Models\Room;
use App\Models\RoomGallery;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class RoomGalleryController extends BackController
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

    public function ajaxGetData(Request $request)
    {
    
       $room_id = $request->room_id;
       $gallery = RoomGallery::where('room_id', $room_id)->get();
       // echo '<pre>'; print_r($gallery); echo '</pre>'; exit;
       return response()->json([
        'status' => 200,
        'html' => view('back.'.config('bookdose.theme_back').'.modules.room.room.box_gallery_list', compact('gallery'))->render(),
       ]);
    }
 
    public function ajaxUploadImage(Request $request)
    {
        $room_id = $request->input('modal_room_id');
        $room_item = Room::find($room_id);
        $validator = \Validator::make($request->all(), 
            [
            'modal_room_gallery_file' => 'required|mimes:jpeg,png,jpg|max:4096',
            ],
            [
              'modal_room_gallery_file.required' => 'Please choose an excel file.',
                'modal_room_gallery_file.max' => 'File size could not be bigger than 4 MB.',
                'modal_room_gallery_file.mimes' => 'File extension must be jpg, jpeg or .png only.'
        ]);
        if ($validator->fails()) {
            return json_encode(array(
                    'status' => 500,
                    'notify_title' => 'Oops!',
                    'msg' => '<div class="text-danger">'.$validator->errors()->first().'</div>',
                    'notify_msg' => $validator->errors()->first(),
                    'notify_icon' => 'icon la la-warning',
                    'notify_type' => 'warning',
               ));
        }

        if ($room_item && $request->hasFile('modal_room_gallery_file')) {
            $data = [];
            $data['room_id'] = $room_item->id;
            $data['is_cover'] = 0;

            $f = $request->file('modal_room_gallery_file');
            $folder_name = 'rooms/'.$room_item->id;
            $file_name = time().'.'.$f->getClientOriginalExtension();
            $file_size = request()->modal_room_gallery_file->getSize();
            $path = $f->store($folder_name);
            if ($path) {
                $data['file_path'] = $path;
                $data['file_size'] = $file_size;
                $item = RoomGallery::create($data);
                if ($item) {
                    $total_img = RoomGallery::where('room_id', $room_item->id)->count();
                    if ($total_img == 1)
                        RoomGallery::where('id', $item->id)->update([ 'is_cover' => 1 ]);

                    return json_encode(array(
                        'status' => 200,
                        'notify_title' => 'Upload Completed!',
                        'notify_msg' => 'Photo been uploaded successfully.',
                        'notify_icon' => 'icon la la-check-circle',
                        'notify_type' => 'success',
                    ));
                }
            }
        }
        return json_encode(array(
            'status' => 500,
            'notify_title' => 'Oops!',
            'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
            'notify_icon' => 'icon la la-warning',
            'notify_type' => 'danger',
        ));
    }

    public function ajaxSetCover(Request $request)
    {
        $id = $request->input('id');
        $room_id = $request->input('room_id');
        RoomGallery::where('room_id', $room_id)->update([ 'is_cover' => 0 ]);
        RoomGallery::where('id', $id)->update([ 'is_cover' => 1 ]);
        return json_encode(array(
            'status' => 200,
            'notify_title' => 'Hooray!',
            'notify_msg' => 'This photo has been set as a cover.',
            'notify_icon' => 'icon la la-check-circle',
            'notify_type' => 'success',
        ));
    }
 
    public function ajaxDeleteImage(Request $request)
    {
        $id = $request->input('id');
        $item = RoomGallery::where('id', $id)->first();
        $rs = RoomGallery::where('id', $id)->delete();
        if ($rs) {
            Storage::delete($item->file_path);
            return json_encode(array(
                'status' => 200,
                'notify_title' => 'Hooray!',
                'notify_msg' => 'Photo has been deleted successfully.',
                'notify_icon' => 'icon la la-check-circle',
                'notify_type' => 'success',
            ));
        }
        
        return json_encode(array(
            'status' => 500,
            'notify_title' => 'Oops!',
            'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
            'notify_icon' => 'icon la la-warning',
            'notify_type' => 'danger',
        ));
    }	
}
