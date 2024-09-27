<div class="pb-10" style="border: 0px !important;">
    <section class="bg-bs-tertiary">
        <div class="container-lg position-relative py-5 " style="z-index: 1;">

            <!-- heading -->
            <div class="header-reward mt-5">
                <div class="display-3 text-dark fw-bold">Rewards Store</div>
                <div class="pb-5 display-3 text-primary fw-bold" style="line-height: 1.5rem">Rewards Today!</div>
                <div class="dropdown">
                    <button class="btn btn-outline-triple dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ (empty($category_id))? 'All Categories': $categories->where('id',$category_id)->first()->title}}
                    </button>
                    <div class="dropdown-menu">
                        @if(!empty($category_id))
                        <a class="dropdown-item dropdown-bs-primary" href="{{ route('reward.index')}}">All Categories</a>
                        @endif
                        @foreach ($categories as $value)
                        <a class="dropdown-item dropdown-bs-primary" href="{{ route('reward.index', ['id' => $value->id])}}">{{ $value->title }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Category items -->
            <div class="row" id="post-data">
                @include('front.'.config('bookdose.theme_front').'.modules.reward.load_reward_item')
            </div>

            <!--- Load more --->
            <div class="justify-content-center position-fixed ajax-load" style="top: 50%; left: 50%; display: none;">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

        </div>
    </section>
</div>

<script>    
    var url = '/reward';
    var totalPage = parseInt("{{$rewards->lastPage()}}");

    function LoadMoreData(page) {
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url + '?page=' + page + '&id={{ $category_id }}',
                type: 'get',
                beforeSend: function() {
                    $(".ajax-load").show();
                }
            })
            .done(function(data) {
                $('.ajax-load').hide();
                $("#post-data").append(data.html);
            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {
                console.log('server not response');
            });
    }

    //have more than 1 page
    if (totalPage > 1) {
        var page = 1;
        $(window).scroll(function() {
            var scroll_height = $(window).scrollTop() + $(window).height();
            var doc_height = $(document).height();
            if (scroll_height >= doc_height && totalPage > page) {
                page++;
                LoadMoreData(page);
            }
        });
    }
</script>