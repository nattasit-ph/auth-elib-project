<?php

namespace App\Http\Controllers\Back\Page;

use DB;
use Auth;
use Session;
use App\Models\Page;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\UserOrg;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Page\PageExport;
use App\Models\PageAttachment;

class PageController extends BackController
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
        $all_category = ArticleCategory::myOrg()->where('system', 'center')->orderBy('title', 'asc')->get();
		return view('back.'.config('bookdose.theme_back').'.modules.page.list');
	}

	public function create()
	{
    	$categories = ArticleCategory::myOrg()->active()->where('system', 'center')->orderBy('title', 'asc')->get();
    	return view('back.'.config('bookdose.theme_back').'.modules.page.form', compact('categories'));
 	}

	public function store(Request $request)
	{
      $validatedData = $request->validate([
          'title_th' => 'required|max:255',
          'title_en' => 'required|max:255',
          'system' => 'required',
          'status' => 'boolean',
          'ref_url' => 'nullable',
          'cover_file_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          'attach_file[]' => 'nullable|mimes:png,gif,jpg,jpeg,pdf,txt,doc,docx,xls,xlsx,ppt,pptx,mp4,mp3,zip|max:204800',
      ]);

        $validatedData['data_blocks'] = $request->description ?? [];
        $validatedData['created_by'] = Auth::user()->id;
        $validatedData['user_org_id'] = Auth::user()->user_org_id;

        if(!empty($request->slug)){
            $validatedData['slug'] = $request->slug;
        }else{
            $validatedData['slug'] = uniqid();
        }

        if(!empty($request->cover_file_path)){
            $validatedData['cover_file_size'] = $request->cover_file_path->getSize();
            $path = $request->cover_file_path->store('pages');
            $validatedData['cover_file_path'] = $path;
        }

        $article = Page::create($validatedData);
        if ($article) {
            //attach file
            if($request->hasFile('attach_file'))
            {
                $files = $request->file('attach_file');
                $folder_name = 'pages/'.$article->id;

                foreach($files as $item)
                {
                    $file_name = uniqid().'.'.$item->getClientOriginalExtension();
                    // $file_size = $file->getSize();
                    $path = $item->storeAs($folder_name, $file_name);
                    if ($path) {
                        $data = [];
                        $data['page_id'] =  $article->id;
                        $data['title'] = $item->getClientOriginalName();
                        $data['file_path'] = $folder_name.'/'.$file_name;
                        $data['file_size'] = $item->getSize();
                        PageAttachment::create($data);

                        //--- Start log ---//
                        $log = collect([ (object)[
                            'module' => 'Page Attachment',
                            'severity' => 'Info',
                            'title' => 'Insert',
                            'desc' => '[Succeeded] - '.$article->title,
                        ]])->first();
                        parent::Log($log);
                        //--- End log ---//
                    }
                }
            }

                //--- Start log ---//
            $log = collect([ (object)[
                'module' => 'Article',
                'severity' => 'Info',
                'title' => 'Insert',
                'desc' => '[Succeeded] - '.$article->title,
            ]])->first();
            parent::Log($log);
            //--- End log ---//
            if ($request->save_option == '1'){
                return redirect()->route('admin.pages.index')->with('success', 'Pages is successfully saved.');
            }else{
                return redirect()->route('admin.pages.create')->with('success', 'Pages is successfully saved.');
            }

        }else{
            return redirect()->route('admin.pages.create')->with('error', 'Oops! Something went wrong. Please refresh this page and then try again.');

        }
 	}

	public function edit($id)
	{
	   $article = Page::with('attachments')->findOrFail($id);
	   return view('back.'.config('bookdose.theme_back').'.modules.page.form', compact('article'));
 	}

	public function update(Request $request, $id)
	{
        $id = $request->input('id');
        $article = Page::findOrFail($id);
        $validatedData = $request->validate([
            'title_th' => 'required|max:255',
            'title_en' => 'required|max:255',
            'system' => 'required',
            'status' => 'boolean',
            'ref_url' => 'nullable',
            'slug' => 'required',
            'cover_file_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'attach_file[]' => 'nullable|mimes:png,gif,jpg,jpeg,pdf,txt,doc,docx,xls,xlsx,ppt,pptx,mp4,mp3,zip|max:204800',
        ]);

        $validatedData['data_blocks'] = $request->description ?? [];
        $validatedData['updated_by'] = Auth::user()->id;

        if ($request->cover_file_path)
        {
            if ($article) Storage::delete($article->cover_file_path);
            $path = $request->cover_file_path->store('pages');
            $validatedData['cover_file_path'] = $path;
        }

        //attach file
        if($request->hasFile('attach_file'))
        {
            $files = $request->file('attach_file');
            $folder_name = 'pages/'.$article->id;

            foreach($files as $item)
            {
                $file_name = uniqid().'.'.$item->getClientOriginalExtension();
				// $file_size = $file->getSize();
				$path = $item->storeAs($folder_name, $file_name);
				if ($path) {
                    $data = [];
                    $data['page_id'] =  $article->id;
                    $data['title'] = $item->getClientOriginalName();
					$data['file_path'] = $folder_name.'/'.$file_name;
					$data['file_size'] = $item->getSize();
					PageAttachment::create($data);

                    //--- Start log ---//
                    $log = collect([ (object)[
                        'module' => 'Article Attachment',
                        'severity' => 'Info',
                        'title' => 'Insert',
                        'desc' => '[Succeeded] - '.$article->title,
                    ]])->first();
                    parent::Log($log);
                    //--- End log ---//
				}
            }
        }

        Page::where('id', $id)->update($validatedData);
        //--- Start log ---//
        $log = collect([ (object)[
            'module' => 'Pages',
            'severity' => 'Info',
            'title' => 'Update',
            'desc' => '[Succeeded] - '.$validatedData['title_th'],
        ]])->first();
        parent::Log($log);
        //--- End log ---//

        return redirect()->route('admin.pages.edit', $id)->with('success', 'Pages is successfully updated.');
	}

	public function setStatus(Request $request)
	{
         $id = $request->input('id');
         if ($id > 0) {
             $item = Page::where('id', $id)->firstOrFail();
             $update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
             $rs = Page::where('id', $id)->update($update_data);
             if ($rs) {
                 //--- Start log ---//
                 $log = collect([ (object)[
                     'module' => 'Pages',
                     'severity' => 'Info',
                     'title' => 'Update status',
                     'desc' => '[Succeeded] - '.$item->title,
                 ]])->first();
                 parent::Log($log);
                 //--- End log ---//

                 return json_encode(array(
                     'status' => 200,
                     'notify_title' => 'Hooray!',
                     'notify_msg' => 'Status has been updated successfully.',
                     'notify_icon' => 'icon la la-check-circle',
                     'notify_type' => 'success',
                 ));
             }
         }
         //--- Start log ---//
         $log = collect([ (object)[
             'module' => 'Pages',
             'severity' => 'Error',
             'title' => 'Update status',
             'desc' => '[Failed] - Invalid id = '.$id,
         ]])->first();
         parent::Log($log);
         //--- End log ---//

         return json_encode(array(
             'status' => 500,
             'notify_title' => 'Oops!',
             'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
             'notify_icon' => 'icon la la-warning',
             'notify_type' => 'danger',
         ));
 	}

	public function ajaxGetData(Request $request)
	{
      $query = Page::myOrg()
              ->select(array_merge(
                  array('pages.*'),
                  array(
                      DB::raw('DATE_FORMAT(pages.created_at, "%d/%m/%Y") AS created_date'),
                  DB::raw('DATE_FORMAT(pages.updated_at, "%d/%m/%Y") AS updated_date')
                  )
              ));

        $system = $request->input('system');
        if(!empty($catesystemgory)){
            $query = $query->where('system', $system);
        }

        $period = $request->input('period');
        switch ($period) {
	    	case 'today':
	    		$query = $query->whereDate('pages.created_at', '=', date("Y-m-d", strtotime('today') ));
	    		break;

	    	case 'yesterday':
	    		$query = $query->whereDate('pages.created_at', '=', date("Y-m-d", strtotime('-1 days') ));
	    		break;

	    	case 'last7Days':
	    		$query = $query->whereDate('pages.created_at', '>', date("Y-m-d", strtotime('-7 days') ));
	    		break;

	    	case 'thisMonth':
	    		$query = $query->whereMonth('pages.created_at', '=', date("m", strtotime('this month') ));
	    		break;

	    	case 'lastMonth':
	    		$query = $query->whereMonth('pages.created_at', '=', date("m", strtotime('last month') ));
	    		break;

	    	case 'customPeriod':
	    		if (!empty($request->period_start)) {
					$date = date_create_from_format("d/m/Y", $request->period_start);
					$query = $query->whereDate('pages.created_at', '>=', date_format($date, "Y-m-d"));
				}
				if (!empty($request->period_end)) {
					$date = date_create_from_format("d/m/Y", $request->period_end);
					$query = $query->whereDate('pages.created_at', '<=', date_format($date, "Y-m-d"));
				}
	    		break;

	    	default:
	    		break;
	    }
      $datatable = new DataTables;
      return $datatable->eloquent($query)

            ->addColumn('title_action_th', function ($article) {
                return '<a href="'.route('admin.pages.edit', $article->id).'" class="">'.$article->title_th.'</a>';
            })
            ->addColumn('title_action_en', function ($article) {
                return '<a href="'.route('admin.pages.edit', $article->id).'" class="">'.$article->title_en.'</a>';
            })
            ->addColumn('slug', function ($article) {
                return $article->slug;
            })

              ->addColumn('status_html', function ($article) {
                  if ($article->status == 1)
                      return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
                  else
                      return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
               })
              ->addColumn('actions', function ($article) {
                $app_url =config('bookdose.app.belib_url');
                  if ($article->status == 1)
                      return '
                          <span class="dropdown">
                            <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                              <i class="la la-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                  <a class="dropdown-item" href="javascript:;" data-id='.json_encode($article->id).' data-status='.json_encode($article->status).' data-title='.json_encode($article->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
                                  <a class="dropdown-item" href="javascript:;" data-url="'.($app_url.'/pages/'.$article->slug).'" onClick="copyUrl(this)"><i class="la la-globe"></i> Copy URL</a>
                                  <a class="dropdown-item" href="javascript:;" data-id='.json_encode($article->id).' data-title='.json_encode($article->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
                            </div>
                        </span>';
                  else
                      return '
                          <span class="dropdown">
                            <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                              <i class="la la-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                  <a class="dropdown-item" href="javascript:;" data-id='.json_encode($article->id).' data-status='.json_encode($article->status).' data-title='.json_encode($article->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
                                  <a class="dropdown-item" href="javascript:;" data-url="'.($app_url.'/pages/'.$article->slug).'" onClick="copyUrl(this)"><i class="la la-globe"></i> Copy URL</a>
                                  <a class="dropdown-item" href="javascript:;" data-id='.json_encode($article->id).' data-title='.json_encode($article->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
                            </div>
                        </span>';
                  })
              ->rawColumns(['title_action_th', 'title_action_en', 'slug', 'status_html', 'actions'])
              ->addIndexColumn()
              ->make(true);
	}

	public function delete(Request $request)
	{
      $id = $request->input('id');
      if ($id > 0) {
          $rs = Page::findOrFail($id);
          $item = $rs;
          if ($rs) Storage::delete($rs->cover_file_path);

          $rs = Page::where('id', $id)->delete();
          if ($rs) {
              //--- Start log ---//
              $log = collect([ (object)[
                  'module' => 'Pages',
                  'severity' => 'Info',
                  'title' => 'Delete',
                  'desc' => '[Succeeded] - '.$item->title,
              ]])->first();
              parent::Log($log);
              //--- End log ---//

              return json_encode(array(
                 'status' => 200,
                 'notify_title' => 'Hooray!',
                  'notify_msg' => $item->title.' has been deleted successfully.',
                  'notify_icon' => 'icon la la-check-circle',
                  'notify_type' => 'success',
              ));
          }
      }
      //--- Start log ---//
      $log = collect([ (object)[
          'module' => 'Pages',
          'severity' => 'Error',
          'title' => 'Delete',
          'desc' => '[Failed] - Invalid id = '.$id,
      ]])->first();
      parent::Log($log);
      //--- End log ---//

      return json_encode(array(
          'status' => 500,
          'notify_title' => 'Oops!',
          'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
          'notify_icon' => 'icon la la-warning',
          'notify_type' => 'danger',
      ));
 	}

    public function previewPage(Request $request)
    {
        $validatedData = $request->validate([
          'title' => 'required|max:255',
        ]);
        $validatedData['description'] = $request->description ?? NULL;
        $content = (object) $validatedData;

        $id = $request->input('id');
        $article = Article::myOrg()->with('creator')->find($id);

        $breadcrumbs = [
          __('article.articles') => route('news.index'),
          $request->title => 'javascript:;',
        ];

        $footer = UserOrg::MyOrg()->first();

        $article_popular = Article::myOrg()->active()
        ->with('categories')
        ->orderBy('total_view', 'DESC')
        ->orderBy('published_at', 'DESC')
        ->limit(3)
        ->get();

        // echo '<pre>'; print_r($content); echo '</pre>'; exit;
        return view('front.' . config('bookdose.theme_front') . '.modules.pages.preview.show')
			->with(compact('footer', 'breadcrumbs', 'article_popular', 'content', 'article'));
    }

    public function exportToExcel(Request $request)
    {
        $system = $request->input('hd_system');
        $period = $request->input('hd_period');
		$custom_period_start = $request->input('hd_custom_period_start');
		$custom_period_end = $request->input('hd_custom_period_end');
		$keyword = $request->input('hd_keyword');

  		return Excel::download(new PageExport($system, $period, $custom_period_start, $custom_period_end, $keyword), 'Pages_'.now().'.xlsx');
    }

    public function fileDelete(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $rs = PageAttachment::findOrFail($id);
            $item = $rs;
            if ($rs) Storage::delete($rs->file_path);

            $rs = PageAttachment::where('id', $id)->delete();
            if ($rs) {
                //--- Start log ---//
                $log = collect([ (object)[
                        'module' => 'Page Attachment',
                        'severity' => 'Info',
                        'title' => 'Delete',
                        'desc' => '[Succeeded] - '.$item->title,
                    ]])->first();
                parent::Log($log);
                //--- End log ---//

                return json_encode(array(
                    'status' => 200,
                    'notify_title' => 'Hooray!',
                    'notify_msg' => $item->title.' has been deleted successfully.',
                    'notify_icon' => 'icon la la-check-circle',
                    'notify_type' => 'success',
                ));
            }
        }
        //--- Start log ---//
        $log = collect([ (object)[
            'module' => 'Page Attachment',
            'severity' => 'Error',
            'title' => 'Delete',
            'desc' => '[Failed] - Invalid id = '.$id,
        ]])->first();
        parent::Log($log);
        //--- End log ---//

        return json_encode(array(
            'status' => 500,
            'notify_title' => 'Oops!',
            'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
            'notify_icon' => 'icon la la-warning',
            'notify_type' => 'danger',
        ));
    }
}
