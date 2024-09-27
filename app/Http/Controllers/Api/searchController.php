<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Article;
use App\Models\ArticleGroup;
use App\Models\ArticleCategory;
use App\Models\Knowledge;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;

class SearchController extends ApiController
{
    // show autocomplete all site
    public function index(Request $request)
    {
        $results = [];
        $word = $request->word ?? '';
        $get = $request->get ?? 8;
        $searchPage = '';
        $site = $request->site ?? '';
        $section = $request->section ?? '';
        // echo "section: ".$section."<br>";
        // exit;

        //learn next
        if (config('bookdose.app.learnext_url') != '' && ($section == '' || $section == 'course')) {
            $response = Http::get(config('bookdose.app.learnext_url') . '/advanced-search-raw', [
                'word' => $word,
                'get' => $get,
            ]);
            if ($response['status'] == 'success') {
                foreach ($response['results'] as $value) {
                    array_push($results, $value);
                }
            }

            //url search page on learn next
            $searchPage =  config('bookdose.app.learnext_url') . '/course/search?word=' . $word;
        }

        //km
        if (config('bookdose.app.km_url') != '' && ($section == '' || $section == 'km')) {
            //get data

            //url search page on km
            $searchPage = '';
        }

        //belib
        if (config('bookdose.app.belib_url') != '')
        {
            if($section == '' || $section == 'library' || $section == 'elibrary') {
                $result = Product::MyOrg()->Active()->with('product_main')
                ->whereHas('product_main', function ($subQuery) {
                    $subQuery->Active();
                })
                ->where(function ($query) use ($word) {
                    $query->where('title', 'LIKE', '%' . $word . '%')
                        ->orWhere(function ($subQuery) use ($word) {
                            $subQuery->withAnyTags([$word], 'tag-' . ((Auth::check()) ? Auth::user()->user_org_id : 1));
                        });
                })
                ->select('slug', 'title', 'cover_file_path', 'product_main_id')
                ->latest()->limit($get)->get()
                ->each(function ($item) {
                    $item->cover_file_path = $item->cover_file_path;
                    $item->product_main_name = $item->product_main->{'name_' . app()->getLocale()};
                    $item->product_main_slug = $item->product_main->{'slug'};
                    $item->url = route('belib.product.show', [$item->product_main->slug, $item->slug]);
                    unset($item->product_main_id);
                    unset($item->product_main);
                });

                foreach ($result as $value) {
                    array_push($results, $value);
                }
            }

            if($section == '' || $section == 'article') {
                //get article
                $arr_child_permission = $this->getChildIDByUserGroup();

                $result = Article::MyOrg()->Active()->select('slug', 'title', 'cover_file_path')
                    ->where('system', 'belib')
                    ->where('title', 'LIKE', '%' . $word . '%')
                    ->permission($arr_child_permission)
                    ->latest()->limit($get)->get()
                    ->each(function ($item) {
                        $item->cover_file_path = url(getCoverImageFromSite(config('bookdose.app.belib_url'), $item->cover_file_path, 'placeholder', true));
                        $item->product_main_name = __('menu.back.article');
                        $item->product_main_slug = 'article';
                        $item->url =  route('belib.article.show', [$item->slug]);
                    });
                foreach ($result as $value) {
                    array_push($results, $value);
                }
            }

            if ($site == 'okmd') {
                //get knowledge
                $result = Knowledge::MyOrg()->Active()->select('slug', 'title', 'cover_file_path')
                    ->where('title', 'LIKE', '%' . $word . '%')
                    ->latest()->limit($get)->get()
                    ->each(function ($item) {
                        $item->cover_file_path = url(getCoverImageFromSite(config('bookdose.app.belib_url'),$item->cover_file_path, 'placeholder', true));
                        $item->product_main_name = __('menu.back.knowledge');
                        $item->product_main_slug = 'knowledge';
                        $item->url =  route('belib.knowledge.show', [$item->slug]);
                    });
                foreach ($result as $value) {
                    array_push($results, $value);
                }
            }

            //url search page on belib
            $searchPage = config('bookdose.app.belib_url') . '/advanced-search?word=' . $word . '&get=' . $get. '&section=' . $section;
        }

        //google book
		if(config('bookdose.search.google_book')) {
            $response = $this->googleBook($word, $get);

            if ($response['status'] == 'success') {
                foreach ($response['results'] as $value) {
                    array_push($results, $value);
                }
            }
        }

        //google search
		if(config('bookdose.search.google_search')) {
			//add google search
			$googleSearch = $this->googleSearch($word, $get);

            if ($response['status'] == 'success') {
                foreach ($response['results'] as $value) {
                    array_push($results, $value);
                }
            }
		}

        shuffle($results);
        $results = array_slice($results, 0, 8);

        //return render result
        return view('front.' . config('bookdose.theme_front') . '.includes.load_search_result', compact('results', 'searchPage'))->render();
    }

	public static function getChildIDByUserGroup()
	{
        // for article more then 1 level
		$article_class = new SearchController;
		$arr_category_name = [];
		if(config('bookdose.article.category_level') > 1) {
			// Get category by user group
			$user_group_id = Auth::user()->user_group_id ?? UserGroup::isDefault()->pluck('id')->first();
			$arr_category_name = $article_class->articleGroupByUserGroup($user_group_id)->pluck('name');
		}

		$result = $article_class->getChildByGroupTitle($arr_category_name)->pluck('id');

		return $result;
	}

	public static function getChildByGroupTitle($title)
	{
        // for article getChildIDByUserGroup call this function
		$group_id = ArticleGroup::MyOrg()->Active()->where('system', 'belib')->whereIn('title', $title)->get()->pluck('id') ?? [];

		$result = ArticleCategory::select('id', 'title', 'slug')->myOrg()->active()->child()->where('system', 'belib')
						->whereIn('group_id', $group_id)
						->orderby('weight')
						->get();

		return $result;
	}

	public static function articleGroupByUserGroup($user_group_id)
	{
        // for article getChildByGroupTitle call this function
		$user_org_id = (Auth::check()) ? Auth::user()->user_org_id : env('DEFAULT_USER_ORG_ID', 1);
		$category_type = 'article-group-category-' . $user_org_id;
		$arr_category_name = UserGroup::where('id', $user_group_id)->first()->tags->where('type', $category_type);

		return $arr_category_name;
	}

    public function search_local_api(Request $request)
    {
        $results = [];
        $word = $request->word ?? '';
        $get = $request->get ?? 8;
        $searchPage = '';
        $site = $request->site ?? '';


        //belib
        if (config('bookdose.app.belib_url') != '') {

            //get product
            $result = Product::MyOrg()->Active()->with('product_main')
                ->whereHas('product_main', function ($subQuery) {
                    $subQuery->Active();
                })
                ->where(function ($query) use ($word) {
                    $query->where('title', 'LIKE', '%' . $word . '%')
                        ->orWhere(function ($subQuery) use ($word) {
                            $subQuery->withAnyTags([$word], 'tag-' . ((Auth::check()) ? Auth::user()->user_org_id : 1));
                        });
                })
                ->select('slug', 'title', 'cover_file_path', 'product_main_id')
                ->latest()->limit($get)->get()
                ->each(function ($item) {
                    $item->cover_file_path = url(getCoverImageFromSite(config('bookdose.app.belib_url'), $item->cover_file_path, 'placeholder', true));
                    $item->product_main_name = $item->product_main->{'name_' . app()->getLocale()};
                    $item->product_main_slug = $item->product_main->{'slug'};
                    $item->url = route('belib.product.show', [$item->product_main->slug, $item->slug]);
                    unset($item->product_main_id);
                    unset($item->product_main);
                });

            foreach ($result as $value) {
                array_push($results, $value);
            }

            //get article
            $result = Article::MyOrg()->Active()->select('slug', 'title', 'cover_file_path')
                ->where('system', 'belib')
                ->where('title', 'LIKE', '%' . $word . '%')
                ->latest()->limit($get)->get()
                ->each(function ($item) {
                    $item->cover_file_path = url(getCoverImageFromSite(config('bookdose.app.belib_url'), $item->cover_file_path, 'placeholder', true));
                    $item->product_main_name = __('menu.back.article');
                    $item->product_main_slug = 'article';
                    $item->url =  route('belib.article.show', [$item->slug]);
                });
            foreach ($result as $value) {
                array_push($results, $value);
            }

            if ($site == 'okmd') {
                //get knowledge
                $result = Knowledge::MyOrg()->Active()->select('slug', 'title', 'cover_file_path')
                    ->where('title', 'LIKE', '%' . $word . '%')
                    ->latest()->limit($get)->get()
                    ->each(function ($item) {
                        $item->cover_file_path = url(getCoverImageFromSite(config('bookdose.app.belib_url'),$item->cover_file_path, 'placeholder', true));
                        $item->product_main_name = __('menu.back.knowledge');
                        $item->product_main_slug = 'knowledge';
                        $item->url =  route('belib.knowledge.show', [$item->slug]);
                    });
                foreach ($result as $value) {
                    array_push($results, $value);
                }
            }

            //url search page on belib
            $searchPage = config('bookdose.app.belib_url') . '/advanced-search?word=' . $word . '&get=' . $get;
        }


        //return render result
        return view('front.' . config('bookdose.theme_front') . '.includes.load_search_result', compact('results', 'searchPage'))->render();
    }

    public function googleSearch($word, $get)
    {
        $const_google_search_cx = "61c3d08c9e722b2a7";
        $const_google_search_key = "AIzaSyB5c-BuOy99Y9yCziYdjWchiogbTE7GJzM";

        $fields = [];
        $fields['cx'] = $const_google_search_cx;
        $fields['key'] = $const_google_search_key;
        $fields['client'] = "google-csbe";
        $fields['safe'] = "ACTIVE"; // SAFE_UNDEFINED, ACTIVE, OFF
        $fields['q'] = $word;
        $fields['num'] = $get; // Number of search results to return
        // $data['start'] = "";
        // $data['sort'] = "date-sdate:d"; // a = ASC, d = DESC
        // $data['filter'] = ""; // 0 = Turns off duplicate content filter, 1 = Turns on duplicate content filter

        $response = Http::get('https://www.googleapis.com/customsearch/v1', $fields);

        $results = [];
        if (!empty($response['items'])) {
            foreach ($response['items'] as $value) {
                $cover_file_path = "";
                if (!empty($value['pagemap'])) {
                    $cover_file_path = $value['pagemap'];
                    if (!empty($cover_file_path['cse_thumbnail'])) {
                        $cover_file_path = $cover_file_path['cse_thumbnail'];
                        if (!empty($cover_file_path['src'])) {
                            $cover_file_path = $cover_file_path['src'];
                            if (!empty($cover_file_path['src'])) {
                                $cover_file_path = $cover_file_path['src'];
                            }
                        }
                    }
                }
                $item = [];
                $item['slug'] = "";
                $item['title'] = $value['title'];
                $item['cover_file_path'] = $cover_file_path;
                $item['product_main_name'] = $value['displayLink']; // "Google Search"
                $item['url'] = $value['link'];

                array_push($results, $item);
            }
        }

        return [
            'status' => 'success',
            'results' => $results,
        ];
    }

    public function googleBook($word, $get)
    {
        $const_google_search_key = "AIzaSyAW7TzHxhPTKhNm93fEaKUgbkRDxw0d41g";

        $fields = [];
        $fields['key'] = $const_google_search_key;
        $fields['q'] = $word;
        $fields['maxResults'] = $get; // Number of search results to return
        $fields['filter'] = "ebooks"; // partial, full, free-ebooks, paid-ebooks, ebooks // https://developers.google.com/books/docs/v1/using#filtering
        $fields['printType'] = "all"; // all (default), books, magazines
        $fields['orderBy'] = "relevance"; // relevance (default), newest
        // print_r($fields);
        // exit;

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', $fields);
        // print_r($response);

        $results = [];
        if (!empty($response['items'])) {
            foreach ($response['items'] as $value) {
                $item = [];
                $item['slug'] = "";
                $item['title'] = $value['volumeInfo']['title'];
                $item['cover_file_path'] = $value['volumeInfo']['imageLinks']['thumbnail'];
                if ($value['volumeInfo']['printType'] == "BOOK" && $value['saleInfo']['isEbook'] == true) {
                    $item['product_main_name'] = "eBook";
                } else if ($value['volumeInfo']['printType'] == "BOOK" && $value['saleInfo']['isEbook'] == false) {
                    $item['product_main_name'] = "Book";
                } else if ($value['volumeInfo']['printType'] == "MAGAZINE" && $value['saleInfo']['isEbook'] == true) {
                    $item['product_main_name'] = "eMagazine";
                } else if ($value['volumeInfo']['printType'] == "MAGAZINE" && $value['saleInfo']['isEbook'] == false) {
                    $item['product_main_name'] = "Magazine";
                } else {
                    $item['product_main_name'] = "";
                }
                $item['url'] = $value['volumeInfo']['previewLink'];

                array_push($results, $item);
            }
        }

        return [
            'status' => 'success',
            'results' => $results,
        ];
    }
}
