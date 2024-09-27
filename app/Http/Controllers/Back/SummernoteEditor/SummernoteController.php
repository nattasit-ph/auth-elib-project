<?php

namespace App\Http\Controllers\Back\SummernoteEditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use File;
use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\File;

class SummernoteController extends Controller
{
   public function __construct()
 	{
 		$this->middleware(function ($request, $next) {
            return $next($request);
		});
 	}

    public function summernoteUploadImage(Request $request)
    {	
        $currFile="";
        $storagePath = storage_path().'/app/public/editor';

        if (!file_exists($storagePath)) {
            File::makeDirectory($storagePath);
        }
        
        $file_name = uniqid();
        // dd($storagePath);
        $photos = request()->files;
        foreach($photos as $index => $p) {
            $currFile = $p;
        }
        
        $currFileName = $currFile->getClientOriginalName();
        $currFileExt = $currFile->getClientOriginalExtension();

        $FilePath = $currFile->move($storagePath, $file_name.".".$currFileExt);
        $ImageLink = url('storage/editor/'.$file_name.".".$currFileExt);
        return $ImageLink;
    }

    public function summernoteRemoveImage(Request $request)
    {	
        // dd($request->src, URL::to("/"));
        $file_name = str_replace(URL::to("/")."/storage/", '', $request->src); // striping host to get relative path\
        if($file_name){
            $rs = Storage::delete($file_name);
            return "Image has been remove.";
        }else{
            return "Image not found.";
        }
        
    }
}
