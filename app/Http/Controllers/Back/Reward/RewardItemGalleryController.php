<?php

namespace App\Http\Controllers\Back\Reward;

use DB;
use Auth;
use App\Models\RewardItem;
use App\Models\RewardItemGallery;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class RewardItemGalleryController extends BackController
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
		$reward_item_id = $request->reward_item_id;
		$gallery = RewardItemGallery::where('reward_item_id', $reward_item_id)->get();
		// echo '<pre>'; print_r($gallery); echo '</pre>'; exit;
		return response()->json([
         'status' => 200,
         'html' => view('back.'.config('bookdose.theme_back').'.modules.reward.reward_item.box_gallery_list', compact('gallery'))->render(),
        ]);
	}

	public function ajaxUploadImage(Request $request)
	{
		$reward_item_id = $request->input('modal_reward_item_id');
		$reward_item = RewardItem::find($reward_item_id);
		$validator = \Validator::make($request->all(), 
			[
            'modal_reward_gallery_file' => 'required|mimes:jpeg,png,jpg|max:4096',
        	],
        	[
          	'modal_reward_gallery_file.required' => 'Please choose an excel file.',
				'modal_reward_gallery_file.max' => 'File size could not be bigger than 4 MB.',
				'modal_reward_gallery_file.mimes' => 'File extension must be jpg, jpeg or .png only.'
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

		if ($reward_item && $request->hasFile('modal_reward_gallery_file')) {
			$data = [];
			$data['reward_item_id'] = $reward_item->id;
			$data['is_cover'] = 0;

			$f = $request->file('modal_reward_gallery_file');
        	$folder_name = 'rewards/'.$reward_item->id;
        	$file_name = time().'.'.$f->getClientOriginalExtension();
        	$file_size = request()->modal_reward_gallery_file->getSize();
        	$path = $f->storeAs($folder_name, $file_name);
			if ($path) {
				$data['file_path'] = $folder_name.'/'.$file_name;
				$data['file_size'] = $file_size;
				$item = RewardItemGallery::create($data);
				if ($item) {
					$total_img = RewardItemGallery::where('reward_item_id', $reward_item->id)->count();
					if ($total_img == 1)
						RewardItemGallery::where('id', $item->id)->update([ 'is_cover' => 1 ]);

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
     	$reward_item_id = $request->input('reward_item_id');
  		RewardItemGallery::where('reward_item_id', $reward_item_id)->update([ 'is_cover' => 0 ]);
  		RewardItemGallery::where('id', $id)->update([ 'is_cover' => 1 ]);
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
  		$item = RewardItemGallery::where('id', $id)->first();
  		$rs = RewardItemGallery::where('id', $id)->delete();
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
