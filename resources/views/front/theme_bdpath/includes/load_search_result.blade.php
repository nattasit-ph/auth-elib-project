<section class="bg-triple overflow-auto load-search-section search-click" style="max-height: 500px;">
    @foreach($product as $value)
    <div class="card m-2 shadow card-search-result" style="max-height: 200px;">
        <div class="row g-0">
            <div class="col-3 col-sm-2 d-flex justify-content-center align-items-center">
                <div class='m-1 m-lg-2'>
                    {!!getCoverImage($value->cover_file_path,$value->product_main->slug,false,'w-100','max-height:70px')!!}
                </div>
            </div>
            <div class="col-9 col-sm-10">
                <div class="card-body h-100 d-flex align-items-center">
                    <a class="text-decoration-none text-dark" href="{{ route('product.show', [$value->product_main->slug, $value->slug]) }}">
                        <p class="stretched-link search-result-text-title text-bs-primary">{{ $value->title }}</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @if($product->count() == 0)
    <p class="text-bs-primary h5 text-center pt-2">{{__('home.not_found')}}</p>
    @else
    <a class="text-dark" href="javascript:;" onclick="openSearchPage2()"><p class="h5 text-center w-100 text-dark my-3">{{__('home.see_all')}}></p></a>
    @endif
</section>