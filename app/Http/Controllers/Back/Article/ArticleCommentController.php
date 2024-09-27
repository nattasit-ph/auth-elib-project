<?php

namespace App\Http\Controllers\Back\Article;

use App\Models\ArticleComment;
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

class ArticleCommentController extends BackController
{
    public function __construct()
	{
		$this->middleware(function ($request, $next) {
			$this->user = Auth::user();
        	// parent::getSiteConfig();

			if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib'])) {
				return $next($request);
			}
			else {
				return redirect()->route('home');
			}
		});
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $article_id = $request->article_id;
        $article = Article::where('id', $article_id)->firstorFail();
		return view('back.'.config('bookdose.theme_back').'.modules.news.comment.list', compact('article'));
    }

    public function ajaxGetData(Request $request)
	{
         $article_id = $request->article_id;
        $order = $request->order;
		$columns = $request->columns;
		if (isset($columns) && isset($order[0]['column'])) {
			Session::put('sort_column', $columns[$order[0]['column']]['name']);
			Session::put('sort_by', $order[0]['dir']);
		}
        $query = ArticleComment::with('creator')
                ->select(array_merge(
                    array('article_comments.*'),
                    array(
                        DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") AS created_date'), 
                    DB::raw('DATE_FORMAT(updated_at, "%d/%m/%Y") AS updated_date') 
                    )
                ))
                ->where('article_id', $article_id);
        // $order = $request->order;
		// $columns = $request->columns;
		// if (isset($columns) && isset($order[0]['column'])) {
		// 	Session::put('sort_column', $columns[$order[0]['column']]['name']);
		// 	Session::put('sort_by', $order[0]['dir']);
		// }

        $period = $request->input('period');
        switch ($period) {
	    	case 'today':
	    		$query = $query->whereDate('created_at', '=', date("Y-m-d", strtotime('today') ));
	    		break;

	    	case 'yesterday':
	    		$query = $query->whereDate('created_at', '=', date("Y-m-d", strtotime('-1 days') ));
	    		break;
	    	
	    	case 'last7Days':
	    		$query = $query->whereDate('created_at', '>', date("Y-m-d", strtotime('-7 days') ));
	    		break;
	    	
	    	case 'thisMonth':
	    		$query = $query->whereMonth('created_at', '=', date("m", strtotime('this month') ));
	    		break;
	    	
	    	case 'lastMonth':
	    		$query = $query->whereMonth('created_at', '=', date("m", strtotime('last month') ));
	    		break;

	    	case 'customPeriod':
	    		if (!empty($request->period_start)) {
					$date = date_create_from_format("d/m/Y", $request->period_start);
					$query = $query->whereDate('created_at', '>=', date_format($date, "Y-m-d"));
				}
				if (!empty($request->period_end)) {
					$date = date_create_from_format("d/m/Y", $request->period_end);
					$query = $query->whereDate('created_at', '<=', date_format($date, "Y-m-d"));
				}
	    		break;
	    	
	    	default:
	    		break;
	    }

      $datatable = new DataTables;
      return $datatable->eloquent($query)
            ->addColumn('created_at', function ($comments) {
                return $comments->created_at;
            })
            ->addColumn('creator', function ($comments) {
                return $comments->creator->name;
            })
            ->addColumn('comment', function ($comments) {
                return $comments->comment;
            })
               
            ->addColumn('status_html', function ($comments) {
                if ($comments->status == 1)
                    return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
                else 
                    return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($comments) {
                if ($comments->status == 1)
                    return '
                        <span class="dropdown">
                          <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                            <i class="la la-ellipsis-h"></i>
                          </a>
                          <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:;" data-id='.json_encode($comments->id).' data-status='.json_encode($comments->status).' data-title='.json_encode($comments->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
                                <a class="dropdown-item" href="javascript:;" data-id='.json_encode($comments->id).' data-title='.json_encode($comments->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
                          </div>
                      </span>';
                else
                    return '
                        <span class="dropdown">
                          <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                            <i class="la la-ellipsis-h"></i>
                          </a>
                          <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:;" data-id='.json_encode($comments->id).' data-status='.json_encode($comments->status).' data-title='.json_encode($comments->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
                                <a class="dropdown-item" href="javascript:;" data-id='.json_encode($comments->id).' data-title='.json_encode($comments->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
                          </div>
                      </span>';
                })
            ->rawColumns(['created_at', 'creator', 'comment', 'status_html', 'actions'])
            ->addIndexColumn()
            ->make(true);
	}
    
    public function setStatus(Request $request)
	{
         $id = $request->input('id');
         if ($id > 0) {
             $item = ArticleComment::where('id', $id)->firstOrFail();
             $update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
             $rs = ArticleComment::where('id', $id)->update($update_data);
             if ($rs) {
                 //--- Start log ---//
                 $log = collect([ (object)[
                     'module' => 'ArticleComment', 
                     'severity' => 'Info', 
                     'title' => 'Update status', 
                     'desc' => '[Succeeded] - '.$item->id,
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
             'module' => 'ArticleComment', 
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

    public function delete(Request $request)
	{
      $id = $request->input('id');
      if ($id > 0) {
          $rs = ArticleComment::findOrFail($id);
          $item = $rs;
        
          
          $rs = ArticleComment::where('id', $id)->delete();
          if ($rs) {
              //--- Start log ---//
              $log = collect([ (object)[
                  'module' => 'ArticleComment', 
                  'severity' => 'Info', 
                  'title' => 'Delete', 
                  'desc' => '[Succeeded] - '.$item->id,
              ]])->first();
              parent::Log($log);
              //--- End log ---//

              return json_encode(array(
                 'status' => 200,
                 'notify_title' => 'Hooray!',
                  'notify_msg' => $item->id.' has been deleted successfully.',
                  'notify_icon' => 'icon la la-check-circle',
                  'notify_type' => 'success',
              ));
          }
      }
      //--- Start log ---//
      $log = collect([ (object)[
          'module' => 'ArticleComment', 
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
     * @param  \App\Models\ArticleComment  $articleComment
     * @return \Illuminate\Http\Response
     */
    public function show(ArticleComment $articleComment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ArticleComment  $articleComment
     * @return \Illuminate\Http\Response
     */
    public function edit(ArticleComment $articleComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ArticleComment  $articleComment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ArticleComment $articleComment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ArticleComment  $articleComment
     * @return \Illuminate\Http\Response
     */
    public function destroy(ArticleComment $articleComment)
    {
        //
    }
}
