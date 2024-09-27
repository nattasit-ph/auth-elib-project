@if (!empty($contents))
    @forelse ($contents as $redemption)
    <div class="card rounded-top-left-lg rounded-bottom-right-lg bg-light mt-5" style="z-index: 1;">
        <div class="card-body">
            <div class="row">
                <div class="col-3 col-md-2 col-sm-3">Date</div>
                <div class="col-3 col-md-2 col-sm-3">Coins</div>
                <div class="col-6 col-md-8 col-sm-6">Reward Item</div>
            </div>
            <div class="row mt-4 font-th-pri">
                <div class="col-3 col-md-2 col-sm-3">{{ date('d M Y', strtotime($redemption->redeemed_at)) ?? '' }}</div>
                <div class="col-3 col-md-2 col-sm-3 text-secondary">-{{ $redemption->total_point ?? '' }}</div>
                <div class="col-6 col-md-8 col-sm-6 d-flex">
                    <div class="col-3 d-none d-sm-none d-md-block d-lg-block">
                        {!! getCoverImage($redemption->rewardItem->rewardGalleries->first()->file_path ?? '', config('bookdose.default_image.cover_community'), false, 'card-img rounded-top-left') !!}
                    </div>
                    <div class="col-9">
                        <div>{{ $redemption->rewardItem->title ?? '' }}</div>
                        @if($redemption->is_delivered)
                            <label class="text-gray mt-3">รับรางวัลแล้ว</label>                            
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-light border">
        <div class="p-5">
        No redemption histories matched your search.
        </div>
    </div>
    @endforelse

    @if ($contents->lastPage() > 1)
    <div class="mt-8 d-flex justify-content-center">
        {{ $contents->links() }}
    </div>
    @endif
@endif
