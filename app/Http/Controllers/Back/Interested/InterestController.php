<?php

namespace App\Http\Controllers\Back\Interested;

use App\Http\Controllers\Back\BackController;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Spatie\Tags\Tag;
use Illuminate\Support\Facades\Auth;
use App\Models\Interested;
use App\Models\Module;
use App\Models\PodcastCategory;
use App\Models\ProductMain;
use App\Models\User;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class InterestController extends BackController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('back.' . config('bookdose.theme_back') . '.modules.interest.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allTagAndCategory = $this->getCategoryAndTag();
        return view('back.' . config('bookdose.theme_back') . '.modules.interest.form', compact('allTagAndCategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'weight' => 'nullable|numeric',
            'status' => 'boolean',
            'file_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'description' => 'required',
        ]);
        $validatedData['created_by'] = Auth::user()->id;
        $validatedData['user_org_id'] = Auth::user()->user_org_id;

        $validatedData = $this->updateCategoryAndTag($request, $validatedData);
        $path = $request->file_path->store('interest');
        if ($path) {
            $validatedData['file_path'] = $path;
            $validatedData['file_size'] = $request->file_path->getSize();

            $interest = Interested::create($validatedData);
            if ($interest) {

                //--- Start log ---//
                $log = collect([(object)[
                    'module' => 'Interested',
                    'severity' => 'Info',
                    'title' => 'Insert',
                    'desc' => '[Succeeded] - ' . $interest->title,
                ]])->first();
                parent::Log($log);
                //--- End log ---//

                if ($request->save_option == '1')
                    return redirect()->route('admin.interest.index')->with('success', 'Interested topic is successfully saved.');
                else
                    return redirect()->route('admin.interest.create')->with('success', 'Interested topic is successfully saved.');
            }
        }
        return redirect()->route('admin.interest.create')->with('error', 'Oops! Something went wrong. Please refresh this page and then try again.');
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $interest = Interested::findOrFail($id);
        $allTagAndCategory = $this->getCategoryAndTag();
        return view('back.' . config('bookdose.theme_back') . '.modules.interest.form', compact('allTagAndCategory','interest'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $interest = Interested::findOrFail($id);
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'weight' => 'nullable|numeric',
            'status' => 'boolean',
            'file_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'description' => 'required',
        ]);
        $validatedData['created_by'] = Auth::user()->id;
        $validatedData['user_org_id'] = Auth::user()->user_org_id;

        $validatedData = $this->updateCategoryAndTag($request, $validatedData);
        if ($request->file_path) {
            if ($interest) Storage::delete($interest->file_path);
            $path = $request->file_path->store('interest');
            if ($path) {
                $validatedData['file_path'] = $path;
                $validatedData['file_size'] = $request->file_path->getSize();
            }
        }

        Interested::where('id', $id)->update($validatedData);
        //--- Start log ---//
        $log = collect([(object)[
            'module' => 'Interested',
            'severity' => 'Info',
            'title' => 'Update',
            'desc' => '[Succeeded] - ' . $interest->title,
        ]])->first();
        parent::Log($log);
        //--- End log ---//

        return redirect()->route('admin.interest.edit', $id)->with('success', 'update topic is successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function ajaxGetData()
    {
        $query = Interested::myOrg();
        $datatable = new DataTables;
        return $datatable->eloquent($query)
            ->addColumn('image', function ($interest) {
                return '<a href="' . route('admin.interest.edit', $interest->id) . '" class="">
		             		<img src="' . Storage::url($interest->file_path) . '" class="img-fluid" style="width:200px">
		             	</a>';
            })
            ->addColumn('title_action', function ($interest) {
                return '<a href="' . route('admin.interest.edit', $interest->id) . '" class="">' . $interest->title . '</a>';
            })
            ->addColumn('status_html', function ($interest) {
                return $interest->status_show;
            })
            ->addColumn('actions', function ($interest) {
                return $interest->status_Action;
            })
            ->rawColumns(['image', 'title_action', 'status_html', 'actions'])
            ->addIndexColumn()
            ->make(true);
    }

    public function setStatus(Request $request)
    {
        $id = $request->input('id');
        $item = Interested::find($id);
        if ($item) {
            $update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
            $interest = Interested::where('id', $id)->update($update_data);
            if ($interest) {
                //--- Start log ---//
                $log = collect([(object)[
                    'module' => 'Interested',
                    'severity' => 'Info',
                    'title' => 'Update status',
                    'desc' => '[Succeeded] - ' . $item->title,
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
        $log = collect([(object)[
            'module' => 'Interested',
            'severity' => 'Error',
            'title' => 'Update status',
            'desc' => '[Failed] - Invalid id = ' . $id,
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
        $item = Interested::find($id);
        if ($item) {
            Storage::delete($item->file_path);
            $interest = Interested::where('id', $id)->delete();
            if ($interest) {
                //--- Start log ---//
                $log = collect([(object)[
                    'module' => 'Interested',
                    'severity' => 'Info',
                    'title' => 'Delete',
                    'desc' => '[Succeeded] - ' . $item->title,
                ]])->first();
                parent::Log($log);
                //--- End log ---//

                return json_encode(array(
                    'status' => 200,
                    'notify_title' => 'Hooray!',
                    'notify_msg' => $item->title . ' has been deleted successfully.',
                    'notify_icon' => 'icon la la-check-circle',
                    'notify_type' => 'success',
                ));
            }
        }
        //--- Start log ---//
        $log = collect([(object)[
            'module' => 'Interested',
            'severity' => 'Error',
            'title' => 'Delete',
            'desc' => '[Failed] - Invalid id = ' . $id,
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

    private function getCategoryAndTag()
    {
        $result = [];

        //belib
        if (config('bookdose.app.belib_url') != '') {
            $result['tag'] = Tag::withType('tag-' . Auth::user()->user_org_id)->ordered()->get();
            if (ProductMain::Haslibrary()) {
                $result['library'] = Tag::withType('library-category-' . Auth::user()->user_org_id)->ordered()->get();
            }
            if (ProductMain::HasElibrary()) {
                $result['elibrary'] = Tag::withType('elibrary-category-' . Auth::user()->user_org_id)->ordered()->get();
            }
            if (Module::MyOrg()->Active()->where('slug', 'article')->exists()) {
                $result['article'] = ArticleCategory::MyOrg()->where('system','belib')->get();
            }
            if (Module::MyOrg()->Active()->where('slug', 'podcast')->exists()) {
                $result['podcast'] = PodcastCategory::MyOrg()->get();
            }
        }

        //elearning
        if (config('bookdose.app.learnext_url') != '') {
            $response =  Http::get(config('bookdose.app.learnext_url') . '/api/v1/category/gettolist');
            if ($response['status'] == 'success') {
                $result['elearning'] = $response['data_result'];
            }
        }

        return $result;
    }

    private function updateCategoryAndTag($request, $validatedData)
    {

        //belib
        if (config('bookdose.app.belib_url') != '') {

            //library
            $library = $request->data_library;
            if (is_null($library)) {
                $validatedData['data_library'] = null;
            }
            if (is_array($library) && count($library) > 0) {
                $validatedData['data_library'] = json_encode($library);
                foreach ($library as $value) {
                    $tag = Tag::findOrCreate($value, 'library-category-' . Auth::user()->user_org_id);
                    $tag->setTranslation('name', 'th', $value);
                    $tag->setTranslation('name', 'en', $value);
                    $tag->save();
                }
            }

            //elibrary
            $elibrary = $request->data_elibrary;
            if (is_null($elibrary)) {
                $validatedData['data_elibrary'] = null;
            }
            if (is_array($elibrary) && count($elibrary) > 0) {
                $validatedData['data_elibrary'] = json_encode($elibrary);
                foreach ($elibrary as $value) {
                    $tag = Tag::findOrCreate($value, 'elibrary-category-' . Auth::user()->user_org_id);
                    $tag->setTranslation('name', 'th', $value);
                    $tag->setTranslation('name', 'en', $value);
                    $tag->save();
                }
            }

            //tag
            $tag = $request->data_tags;
            if (is_null($tag)) {
                $validatedData['data_tags'] = null;
            }
            if (is_array($tag) && count($tag) > 0) {
                $validatedData['data_tags'] = json_encode($tag);
                foreach ($tag as $value) {
                    $tag = Tag::findOrCreate($value, 'tag-' . Auth::user()->user_org_id);
                    $tag->setTranslation('name', 'th', $value);
                    $tag->setTranslation('name', 'en', $value);
                    $tag->save();
                }
            }

            //article
            $article = $request->data_article;
            if (is_null($article)) {
                $validatedData['data_article'] = null;
            }
            if (is_array($article) && count($article) > 0) {
                $validatedData['data_article'] = json_encode($article);
            }

            //podcast
            $podcast = $request->data_podcast;
            if (is_null($podcast)) {
                $validatedData['data_podcast'] = null;
            }
            if (is_array($podcast) && count($podcast) > 0) {
                $validatedData['data_podcast'] = json_encode($podcast);
            }
        }

        //elearning
        if (config('bookdose.app.learnext_url') != '') {
            $elearning = $request->data_elearning;
            if (is_null($elearning)) {
                $validatedData['data_elearning'] = null;
            }
            if (is_array($elearning) && count($elearning) > 0) {
                $validatedData['data_elearning'] = json_encode($elearning);
            }
        }

        return $validatedData;
    }
}
