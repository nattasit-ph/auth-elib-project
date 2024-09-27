<?php

namespace App\Http\Controllers\Front\Interested;

use App\Http\Controllers\Front\FrontController;
use App\Models\Article;
use App\Models\CourseCategory;
use App\Models\Interested;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserOrg;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Tags\Tag;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Module;
use App\Models\Course;
use App\Models\PodcastStation;
use Illuminate\Support\Facades\Schema;

class InterestController extends FrontController
{
    public function index(Request $request)
    {
        $interestedTopic = Interested::MyOrg()->Active()->get();
		$breadcrumbs = [
			__('menu.front.my_interests') => route('interest.index')
		];
        $footer = UserOrg::myOrg()->with(['questionBelib', 'questionKm', 'questionLearnext'])->first();
        return view('front.' . config('bookdose.theme_front') . '.modules.interested.main')
            ->with(compact('breadcrumbs', 'footer', 'interestedTopic'));
    }

    public function updateInterested(Request $request)
    {
        $myInterest = $request->myInterest ?? array();
        if (is_array($myInterest)) {
            try {
                $q = User::MySelf()->update([
                    'data_interested' => (count($myInterest) == 0) ? null : json_encode($myInterest),
                ]);
                if ($q) {
                    return response()->json([
                        'status' => 'success',
                    ]);
                }
            } catch (\Exception $e) {
            }
        }
        return response()->json([
            'status' => 'error',
        ]);;
    }

    public function getMyInterested(Request $request)
    {
        //get user interest topic
        $user = User::MySelf()->firstOrFail();
        if (!is_null($user->data_interested)) {
            $myInterest = Interested::Active()->get()->whereIn('id', json_decode($user->data_interested));
        } else {
            $myInterest = Interested::Active()->get();
        }
        $results = array();
        $haveNextPage = false;

        //belib
        if (config('bookdose.app.belib_url') != '') {

            //get elibrary category
            $elibraryCategory = $myInterest->pluck('data_elibrary');
            $Arr = array();
            foreach ($elibraryCategory as $value) {
                if (!is_null(json_decode($value))) {
                    $Arr = array_merge($Arr, json_decode($value));
                }
            }
            $elibraryCategory = Tag::where('type', 'elibrary-category-' . Auth::user()->user_org_id)->get()->whereIn('name', array_unique($Arr));

            //get library category
            $libraryCategory = $myInterest->pluck('data_library');
            $Arr = array();
            foreach ($libraryCategory as $value) {
                if (!is_null(json_decode($value))) {
                    $Arr = array_merge($Arr, json_decode($value));
                }
            }
            $libraryCategory = Tag::where('type', 'library-category-' . Auth::user()->user_org_id)->get()->whereIn('name', array_unique($Arr));

            //get tag
            $tag = $myInterest->pluck('data_tags');
            $Arr = array();
            foreach ($tag as $value) {
                if (!is_null(json_decode($value))) {
                    $Arr = array_merge($Arr, json_decode($value));
                }
            }
            $tag = Tag::where('type', 'tag-' . Auth::user()->user_org_id)->get()->whereIn('name', array_unique($Arr));

            //get product on belib
            $allTagId = $elibraryCategory->merge($libraryCategory)->merge($tag)->pluck('id');
            $product = $this->getProductByCategory($allTagId, $request);
            if ($product['haveNextPage']) {
                $haveNextPage = true;
            }
            foreach ($product['result'] as $value) {
                array_push($results, $value);
            }

            //article
            if (Module::MyOrg()->Active()->where('slug', 'article')->exists()) {

                //article category
                $articleCategory = $myInterest->pluck('data_article');
                $Arr = array();
                foreach ($articleCategory as $value) {
                    if (!is_null(json_decode($value))) {
                        $Arr = array_merge($Arr, json_decode($value));
                    }
                }

                //get article
                $article = $this->getArticleByCategory($Arr, $request);
                if ($article['haveNextPage']) {
                    $haveNextPage = true;
                }
                foreach ($article['result'] as $value) {
                    array_push($results, $value);
                }
            }

            //podcast
            if (Module::MyOrg()->Active()->where('slug', 'podcast')->exists()) {

                //article category
                $podcastCategory = $myInterest->pluck('data_podcast');
                $Arr = array();
                foreach ($podcastCategory as $value) {
                    if (!is_null(json_decode($value))) {
                        $Arr = array_merge($Arr, json_decode($value));
                    }
                }

                //get podcast
                $podcast = $this->getPodcastByCategory($Arr, $request);
                if ($podcast['haveNextPage']) {
                    $haveNextPage = true;
                }
                foreach ($podcast['result'] as $value) {
                    array_push($results, $value);
                }
            }
        }

        //elearning
        if (config('bookdose.app.learnext_url') != '') {

            //get elearning category
            $elearningCategory = $myInterest->pluck('data_elearning');
            $Arr = array();
            foreach ($elearningCategory as $value) {
                if (!is_null(json_decode($value))) {
                    $Arr = array_merge($Arr, json_decode($value));
                }
            }

            //get course
            $elearning = $this->getElearningByCategory($Arr, $request);
            if ($elearning['haveNextPage']) {
                $haveNextPage = true;
            }
            foreach ($elearning['result'] as $value) {
                array_push($results, $value);
            }
        }

        //return render result unset temp token
        $q = User::MySelf()->update(['temp_token' => null]);
        shuffle($results);
        return view('front.' . config('bookdose.theme_front') . '.modules.interested.load_interest_product', compact('results', 'haveNextPage'))
            ->render();
    }

    public function getTypeByProductMain($productMainSlug)
    {
        switch ($productMainSlug) {
            case 'book':
            case 'magazine':
            case 'ebook':
            case 'emagazine':
                return 'read';
                break;
            case 'audio-book':
                return 'listen';
                break;
            case 'multimedia':
                return 'watch';
                break;
            default:
                return '';
        }
    }

    public function getElearningByCategory($categorySlugArr, $request)
    {
        $haveNextPage = false;
        $elearning = Course::Active()->whereHas('categories', function ($query) use ($categorySlugArr) {
            $query->whereIn('slug', $categorySlugArr);
        })->select('id','slug', 'title', 'cover_image_path')
            ->latest()->paginate(6);
        if ($elearning->lastPage()  > intval($request->page)) {
            $haveNextPage = true;
        }
        $elearning = $elearning->each(function ($item) {
            $item->cover_file_path = $item->cover_image_path;
            $item->product_main_slug = 'course';
            $item->url = route('learnext.show', [$item->slug]);
            $item->description = '';
            $item->type = '';
            if (Schema::hasTable('ref_course_extra_sites')) {
                $site = DB::table('ref_course_extra_sites')->where('course_id',$item->id)->first();
                $item->source_form = '';
                if($site)
                {
                    $item->source_form = $site->site;
                }
            }
            unset($item->cover_image_path);
        })->toArray();

        return array(
            'haveNextPage' => $haveNextPage,
            'result' => $elearning
        );
    }

    public function getArticleByCategory($categorySlugArr, $request)
    {
        $haveNextPage = false;
        $article = Article::MyOrg()->Active()->where('system', 'belib')->whereHas('categories', function ($query) use ($categorySlugArr) {
            $query->whereIn('slug', $categorySlugArr);
        })->select('id','slug', 'title', 'cover_file_path', 'excerpt')
            ->latest()->paginate(6);
        if ($article->lastPage()  > intval($request->page)) {
            $haveNextPage = true;
        }
        $article = $article->each(function ($item) {
            $item->product_main_slug = 'article';
            $item->url = route('belib.article.show', [$item->slug]);
            $item->description = $item->excerpt;
            $item->type = 'read';
            $item->source_form = '';
            unset($item->excerpt);
        })->toArray();

        return array(
            'haveNextPage' => $haveNextPage,
            'result' => $article
        );
    }

    public function getPodcastByCategory($categorySlugArr, $request)
    {
        $haveNextPage = false;
        $podcast = PodcastStation::MyOrg()->Active()->whereHas('categories', function ($query) use ($categorySlugArr) {
            $query->whereIn('slug', $categorySlugArr);
        })->select('id','slug', 'title', 'cover_file_path')
            ->latest()->paginate(6);
        if ($podcast->lastPage()  > intval($request->page)) {
            $haveNextPage = true;
        }
        $podcast = $podcast->each(function ($item) {
            $item->product_main_slug = 'podcast';
            $item->url = route('belib.podcast.show', [$item->slug]);
            $item->description = '';
            $item->type = 'listen';
            $item->source_form = '';
            unset($item->excerpt);
        })->toArray();

        return array(
            'haveNextPage' => $haveNextPage,
            'result' => $podcast
        );
    }

    public function getProductByCategory($allTagId, $request)
    {
        $haveNextPage = false;
        $allProductId = DB::table('taggables')
            ->select('taggable_id as id')
            ->where('taggable_type', 'App\Models\Product')
            ->whereIn('tag_id', $allTagId)
            ->get()->unique('id');
        $product = Product::MyOrg()->Active()->whereIn('id', $allProductId->pluck('id'))
            ->select('slug', 'title', 'cover_file_path', 'product_main_id', 'data_fields')
            ->latest()->paginate(12);
        if ($product->lastPage() > intval($request->page)) {
            $haveNextPage = true;
        }
        $product = $product->each(function ($item) {
            $item->product_main_slug = $item->product_main->slug;
            $item->url = route('belib.product.show', [$item->product_main->slug, $item->slug]);
            $item->description = $item->data_fields['description'] ?? '';
            $item->type = $this->getTypeByProductMain($item->product_main_slug);
            unset($item->product_main_id);
            unset($item->product_main);
        })->toArray();

        return array(
            'haveNextPage' => $haveNextPage,
            'result' => $product
        );
    }

    function saveCookie(){

        session_start();
        $_SESSION["cookie_active"] = 1;
        $cookie_active = $_SESSION["cookie_active"];

        echo json_encode($cookie_active);
    }


    public function privacy_and_policy()
    {
        $interestedTopic = Interested::MyOrg()->Active()->get();
        $breadcrumbs = [
            __('menu.front.my_interests') => route('interest.index')
        ];

        $footer = UserOrg::myOrg()->with(['questionBelib', 'questionKm', 'questionLearnext'])->first();
        return view('front.theme_cookie.privacy_and_policy')
            ->with(compact('breadcrumbs', 'footer', 'interestedTopic'));
    }


    public function terms_and_conditions()
    {
        $interestedTopic = Interested::MyOrg()->Active()->get();
        $breadcrumbs = [
            __('menu.front.my_interests') => route('interest.index')
        ];

        $footer = UserOrg::myOrg()->with(['questionBelib', 'questionKm', 'questionLearnext'])->first();
        return view('front.theme_cookie.terms_and_conditions')
            ->with(compact('breadcrumbs', 'footer', 'interestedTopic'));
    }
    public function download_policy()
    {
        $app_name = strtolower(config('bookdose.app.name'));
        $file = asset('files/pdf/'.$app_name.'_personal_information_request_form.pdf');
        return redirect($file);
    }

}
