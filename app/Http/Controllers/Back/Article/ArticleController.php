<?php

namespace App\Http\Controllers\Back\Article;

use DB;
use Auth;
use Session;
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
use App\Exports\Article\ArticleExport;

class ArticleController extends BackController
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
		return view('back.'.config('bookdose.theme_back').'.modules.news.list', compact('all_category'));
	}

	public function create()
	{
    	$categories = ArticleCategory::myOrg()->active()->where('system', 'center')->orderBy('title', 'asc')->get();
    	return view('back.'.config('bookdose.theme_back').'.modules.news.form', compact('categories'));
 	}

	public function store(Request $request)
	{
      $validatedData = $request->validate([
          'title' => 'required|max:255',
          'status' => 'boolean',
          'excerpt' => 'nullable',
          'ref_url' => 'nullable',
          'cover_file_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          'is_recommended' => 'nullable',
          'creator' => 'nullable',
      ]);
        if(empty($validatedData['is_recommended'])){
            $validatedData['is_recommended'] = 0;
        }
      $validatedData['system'] = 'center';
      $validatedData['data_blocks'] = $request->description ?? [];
      $validatedData['created_by'] = Auth::user()->id;
      $validatedData['user_org_id'] = Auth::user()->user_org_id;
      $validatedData['slug'] = uniqid();
      
		if (!empty($request->published_date)) {
			$date = date_create_from_format("d/m/Y", $request->published_date);
			$validatedData['published_at'] =  date_format($date, "Y-m-d");
		}
		else {
			$today = today();
			$validatedData['published_at'] =  date_format($today, "Y-m-d");
		}

      $path = $request->cover_file_path->store('news');
      if ($path) {
          $validatedData['cover_file_path'] = $path;

          $article = Article::create($validatedData);
          if ($article) {
          	// Sync categories
      		$article->categories()->sync($request->article_categories);

             //--- Start log ---//
             $log = collect([ (object)[
                 'module' => 'Article', 
                 'severity' => 'Info', 
                 'title' => 'Insert', 
                 'desc' => '[Succeeded] - '.$article->title,
             ]])->first();
             parent::Log($log);
             //--- End log ---//

          }
          if ($request->save_option == '1')
              return redirect()->route('admin.news.index')->with('success', 'Article is successfully saved.');
          else
              return redirect()->route('admin.news.create')->with('success', 'Article is successfully saved.');
      }
      else {
          return redirect()->route('admin.news.create')->with('error', 'Oops! Something went wrong. Please refresh this page and then try again.');
      }
 	}

	public function edit($id)
	{
	   $article = Article::with('categories')->findOrFail($id);
	   $categories = ArticleCategory::myOrg()->active()->where('system', 'center')->orderBy('title', 'asc')->get();
	   $selected_categories = [];
	   if (!empty($article->categories)) {
	   	foreach ($article->categories as $cat) {
	   		$selected_categories[] = $cat['pivot']['article_category_id'];
	   	}
	   }
	   $article->selected_categories = $selected_categories;
	   return view('back.'.config('bookdose.theme_back').'.modules.news.form', compact('article', 'categories'));
 	}

	public function update(Request $request, $id)
	{
      $id = $request->input('id');
      $article = Article::findOrFail($id);
      $validatedData = $request->validate([
          'title' => 'required|max:255',
          'status' => 'boolean',
          'excerpt' => 'nullable',
          'ref_url' => 'nullable',
          'cover_file_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
          'is_recommended' => 'nullable',
          'creator' => 'nullable',
      ]);
      if(empty($validatedData['is_recommended'])){
            $validatedData['is_recommended'] = 0;
      }
      $validatedData['data_blocks'] = $request->description ?? [];
      $validatedData['updated_by'] = Auth::user()->id;

		if (!empty($request->published_date)) {
			$date = date_create_from_format("d/m/Y", $request->published_date);
			$validatedData['published_at'] =  date_format($date, "Y-m-d");
		}
		else {
			$today = today();
			$validatedData['published_at'] =  date_format($today, "Y-m-d");
		}

      if ($request->cover_file_path) 
      {
          $rs = Article::findOrFail($id);
          if ($rs) Storage::delete($rs->cover_file_path);
          $path = $request->cover_file_path->store('news');
          $validatedData['cover_file_path'] = $path;
      }

      Article::where('id', $id)->update($validatedData);
      // Sync categories
      $article->categories()->sync($request->article_categories);

      //--- Start log ---//
      $log = collect([ (object)[
          'module' => 'Article', 
          'severity' => 'Info', 
          'title' => 'Update', 
          'desc' => '[Succeeded] - '.$validatedData['title'],
      ]])->first();
      parent::Log($log);
      //--- End log ---//

      return redirect()->route('admin.news.edit', $id)->with('success', 'Article is successfully updated.');
	}

	public function setStatus(Request $request)
	{
         $id = $request->input('id');
         if ($id > 0) {
             $item = Article::where('id', $id)->firstOrFail();
             $update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
             $rs = Article::where('id', $id)->update($update_data);
             if ($rs) {
                 //--- Start log ---//
                 $log = collect([ (object)[
                     'module' => 'Article', 
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
             'module' => 'Article', 
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
      $query = Article::myOrg()->where('system', 'center')
              ->select(array_merge(
                  array('articles.*'),
                  array(
                      DB::raw('DATE_FORMAT(articles.created_at, "%d/%m/%Y") AS created_date'), 
                  DB::raw('DATE_FORMAT(articles.updated_at, "%d/%m/%Y") AS updated_date') 
                  )
              ));

        $category = $request->input('category');
        if(!empty($category)){
            $query = $query->join('ref_article_categories', 'ref_article_categories.article_id', '=', 'articles.id')
                            ->where('ref_article_categories.article_category_id', $category);
        }

        $period = $request->input('period');
        switch ($period) {
	    	case 'today':
	    		$query = $query->whereDate('articles.published_at', '=', date("Y-m-d", strtotime('today') ));
	    		break;

	    	case 'yesterday':
	    		$query = $query->whereDate('articles.published_at', '=', date("Y-m-d", strtotime('-1 days') ));
	    		break;
	    	
	    	case 'last7Days':
	    		$query = $query->whereDate('articles.published_at', '>', date("Y-m-d", strtotime('-7 days') ));
	    		break;
	    	
	    	case 'thisMonth':
	    		$query = $query->whereMonth('articles.published_at', '=', date("m", strtotime('this month') ));
	    		break;
	    	
	    	case 'lastMonth':
	    		$query = $query->whereMonth('articles.published_at', '=', date("m", strtotime('last month') ));
	    		break;

	    	case 'customPeriod':
	    		if (!empty($request->period_start)) {
					$date = date_create_from_format("d/m/Y", $request->period_start);
					$query = $query->whereDate('articles.published_at', '>=', date_format($date, "Y-m-d"));
				}
				if (!empty($request->period_end)) {
					$date = date_create_from_format("d/m/Y", $request->period_end);
					$query = $query->whereDate('articles.published_at', '<=', date_format($date, "Y-m-d"));
				}
	    		break;
	    	
	    	default:
	    		break;
	    }
      $datatable = new DataTables;
      return $datatable->eloquent($query)
              ->addColumn('image', function ($article) {
                   return '<a href="'.route('admin.news.edit', $article->id).'" class="">'.
	             		getCoverImage($article->cover_file_path, 'article', false, 'img-fluid').'
	             	</a>';
              })
              ->addColumn('title_action', function ($article) {
                   return '<a href="'.route('admin.news.edit', $article->id).'" class="">'.$article->title.'</a>';
               })
               
              ->addColumn('status_html', function ($article) {
                  if ($article->status == 1)
                      return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
                  else 
                      return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
               })
              ->addColumn('actions', function ($article) {
                  if ($article->status == 1)
                      return '
                          <span class="dropdown">
                            <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                              <i class="la la-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                  <a class="dropdown-item" href="javascript:;" data-id='.json_encode($article->id).' data-status='.json_encode($article->status).' data-title='.json_encode($article->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
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
                                  <a class="dropdown-item" href="javascript:;" data-id='.json_encode($article->id).' data-title='.json_encode($article->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
                            </div>
                        </span>';
                  })
              ->rawColumns(['image', 'title_action', 'status_html', 'actions'])
              ->addIndexColumn()
              ->make(true);
	}

	public function delete(Request $request)
	{
      $id = $request->input('id');
      if ($id > 0) {
          $rs = Article::findOrFail($id);
          $item = $rs;
          if ($rs) Storage::delete($rs->cover_file_path);
          
          $rs = article::where('id', $id)->delete();
          if ($rs) {
              //--- Start log ---//
              $log = collect([ (object)[
                  'module' => 'Article', 
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
          'module' => 'Article', 
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
        return view('front.' . config('bookdose.theme_front') . '.modules.news.preview.show')
			->with(compact('footer', 'breadcrumbs', 'article_popular', 'content', 'article'));
    }
    
    public function exportToExcel(Request $request)
    {
        $category = $request->input('hd_category');
        $period = $request->input('hd_period');
		$custom_period_start = $request->input('hd_custom_period_start');
		$custom_period_end = $request->input('hd_custom_period_end');
		$keyword = $request->input('hd_keyword');
  		return Excel::download(new ArticleExport($category, $period, $custom_period_start, $custom_period_end, $keyword), 'News_'.now().'.xlsx');
    }
}
