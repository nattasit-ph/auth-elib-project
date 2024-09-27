<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Interested\InterestController;
use App\Models\Interested;
use App\Models\User;
use App\Models\UserOrg;
use App\Models\Module;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Tags\Tag;

class InterestTopicController extends ApiController
{
    public function allTopic()
    {
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
        if (empty(request()->token))
            $return_data['msg'] = 'Missing token';
        elseif (!$payload = parent::parseJWT())
            $return_data['msg'] = 'Invalid token';

        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        // 2. Query
        $user = User::where('id', $payload->id)->Active()->firstOrFail();
        $interestTopic = Interested::Active()->where('user_org_id', $user->user_org_id)->select('id', 'title', 'description', 'file_path', 'weight')->get()
            ->each(function ($item, $key) {
                $item->file_path = url('/storage/' . $item->file_path);
            })->toArray();

        return response()->json([
            'status' => 'success',
            'results' => $interestTopic ?? [],
        ]);
    }

    public function userInserestTopic()
    {
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
        if (empty(request()->token))
            $return_data['msg'] = 'Missing token';
        elseif (!$payload = parent::parseJWT())
            $return_data['msg'] = 'Invalid token';

        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        // 2. Query
        $user = User::where('id', $payload->id)->Active()->firstOrFail();
        $userInterestTopic = json_decode($user->data_interested);
        if (!is_array($userInterestTopic))
            $userInterestTopic = [];
            $interestTopic = Interested::Active()->where('user_org_id', $user->user_org_id)->select('id', 'title', 'description', 'file_path', 'weight')->get()
            ->each(function ($item) use ($userInterestTopic) {
                $item->file_path = url('/storage/' . $item->file_path);
                if (in_array($item->id,  $userInterestTopic)) {
                    $item->status = 1;
                } else {
                    $item->status = 0;
                }
            })->toArray();

        return response()->json([
            'status' => 'success',
            'results' => $interestTopic ?? [],
        ]);
    }

    public function updateUserInserestTopic()
    {
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
        if (empty(request()->token))
            $return_data['msg'] = 'Missing token';
        elseif (!$payload = parent::parseJWT())
            $return_data['msg'] = 'Invalid token';

        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        // 2. Query
        if (strpos(request()->interest, ',')) {
            $userInterest = explode(',', request()->interest);
        } else {
            $userInterest = (empty(request()->interest)) ? null : array(request()->interest);
        }
        $user = User::where('id', $payload->id)->Active()->firstOrFail();
        $user->data_interested = $userInterest;
        $user->save();

        return response()->json([
            'status' => 'success',
            'results' => 'user interest topic id ' . request()->interest,
        ]);
    }

    public function product()
    {
        // 1. Pre-check parameters
        $return_data = array('status' => 'error', 'msg' => '');
        if (empty(request()->token))
            $return_data['msg'] = 'Missing token';
        elseif (!$payload = parent::parseJWT())
            $return_data['msg'] = 'Invalid token';

        if (!empty($return_data['msg'])) {
            return response()->json($return_data);
        }

        // 2. Query
        $user = User::where('id', $payload->id)->Active()->firstOrFail();
        if (!is_null($user->data_interested)) {
            $myInterest = Interested::Active()->get()->whereIn('id', json_decode($user->data_interested));
        } else {
            $myInterest = Interested::Active()->get();
        }
        $results = array();
        $haveNextPage = false;
        $interestController = new InterestController;
        $type = explode(",", request()->type);

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
            $productMain = array();
            foreach ($type as $value) {
                switch ($value) {
                    case 'read':
                        array_push($productMain, 'book', 'magazine', 'ebook', 'emagazine');
                        break;
                    case 'listen':
                        array_push($productMain, 'audio-book');
                        break;
                    case 'watch':
                        array_push($productMain, 'multimedia');
                        break;
                    default:
                        break;
                }
            }
            $allTagId = $elibraryCategory->merge($libraryCategory)->merge($tag)->pluck('id');
            $allProductId = DB::table('taggables')
                ->select('taggable_id as id')
                ->where('taggable_type', 'App\Models\Product')
                ->whereIn('tag_id', $allTagId)
                ->get()->unique('id');
            $product = Product::MyOrg()->Active()->whereIn('id', $allProductId->pluck('id'))
                ->whereHas('product_main', function ($query) use ($productMain) {
                    $query->whereIn('slug', $productMain);
                })
                ->select('products.id','slug', 'title', 'cover_file_path', 'product_main_id', 'data_fields')
                ->latest()->paginate(12);
            if ($product->lastPage() > intval(request()->page)) {
                $haveNextPage = true;
            }
            $product = $product->each(function ($item) {
                $item->product_main_slug = $item->product_main->slug;
                $item->url = route('belib.product.show', [$item->product_main->slug, $item->slug]);
                $item->description = $item->data_fields['description'] ?? '';
                $item->source_form = $item->data_fields['source_form'] ?? '';
                unset($item->product_main_id);
                unset($item->product_main);
            })->toArray();
            foreach ($product as $value) {
                if (!empty($value['cover_file_path']) && !(Str::contains($value['cover_file_path'], ['http://', 'https://']))) {
                    $value['cover_file_path'] = config('bookdose.app.belib_url') . '/storage/' . $value['cover_file_path'];
                }else
                {
                    if(!(Str::contains($value['cover_file_path'], ['http://', 'https://'])))
                    {
                        $value['cover_file_path'] =  "";
                    }
                }
                array_push($results, $value);
            }

            //article
            if (Module::MyOrg()->Active()->where('slug', 'article')->exists() && in_array("read", $type)) {

                //article category
                $articleCategory = $myInterest->pluck('data_article');
                $Arr = array();
                foreach ($articleCategory as $value) {
                    if (!is_null(json_decode($value))) {
                        $Arr = array_merge($Arr, json_decode($value));
                    }
                }

                //get article
                $article = $interestController->getArticleByCategory($Arr, request());
                if ($article['haveNextPage']) {
                    $haveNextPage = true;
                }
                foreach ($article['result'] as $value) {
                    if (!empty($value['cover_file_path']) && !(Str::contains($value['cover_file_path'], ['http://', 'https://']))) {
                        $value['cover_file_path'] = config('bookdose.app.belib_url') . '/storage/' . $value['cover_file_path'];
                    }else
                    {
                        if(!(Str::contains($value['cover_file_path'], ['http://', 'https://'])))
                        {
                            $value['cover_file_path'] =  "";
                        }
                    }
                    
                    array_push($results, $value);
                }
            }

            //podcast
            if (Module::MyOrg()->Active()->where('slug', 'podcast')->exists() && in_array("listen", $type)) {

                //article category
                $podcastCategory = $myInterest->pluck('data_podcast');
                $Arr = array();
                foreach ($podcastCategory as $value) {
                    if (!is_null(json_decode($value))) {
                        $Arr = array_merge($Arr, json_decode($value));
                    }
                }

                //get podcast
                $podcast = $interestController->getPodcastByCategory($Arr, request());
                if ($podcast['haveNextPage']) {
                    $haveNextPage = true;
                }
                foreach ($podcast['result'] as $value) {
                    if (!empty($value['cover_file_path']) && !(Str::contains($value['cover_file_path'], ['http://', 'https://']))) {
                        $value['cover_file_path'] = config('bookdose.app.belib_url') . '/storage/' . $value['cover_file_path'];
                    }
                    else
                    {
                        if(!(Str::contains($value['cover_file_path'], ['http://', 'https://'])))
                        {
                            $value['cover_file_path'] =  "";
                        }
                    }
                    array_push($results, $value);
                }
            }
        }

        //elearning
        if (config('bookdose.app.learnext_url') != '' && in_array("watch", $type)) {

            //get elearning category
            $elearningCategory = $myInterest->pluck('data_elearning');
            $Arr = array();
            foreach ($elearningCategory as $value) {
                if (!is_null(json_decode($value))) {
                    $Arr = array_merge($Arr, json_decode($value));
                }
            }

            //get course
            $elearning = $interestController->getElearningByCategory($Arr, request());
            if ($elearning['haveNextPage']) {
                $haveNextPage = true;
            }
            foreach ($elearning['result'] as $value) {
                array_push($results, $value);
            }
        }

        shuffle($results);
        return response()->json([
            'status' => 'success',
            'haveNextPage' => $haveNextPage,
            'results' => $results ?? '',
        ]);
    }
}
