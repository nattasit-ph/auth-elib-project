@if (!empty($contents))
    @if ($contents->count() > 0)
    <div class="card bg-secondary text-white" style="z-index: 1;">
        <div class="p-4"><!-- card-body-->
            <div class="row">
                <div class="col-3 col-md-2 col-sm-3">Date</div>
                <div class="col-3 col-md-2 col-sm-3">Coins</div>
                <div class="col-6 col-md-8 col-sm-6">Activity</div>
            </div>
        </div>
    </div>
    @endif
    @forelse ($contents as $earning)
    <div class="" style="z-index: 1;">
        <div class="pt-5 pb-5 pl-5">
            <div class="row font-th-pri">
                <div class="col-3 col-md-2 col-sm-3">{{ date('d M Y', strtotime($earning->created_at)) ?? '' }}</div>
                <div class="col-3 col-md-2 col-sm-3 text-secondary">{{ $earning->point ?? '' }}</div>
                <div class="col-6 col-md-8 col-sm-6 d-flex">
                        <div>{{ $earning->rewardActivity->title ?? '' }}</div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-light border">
        <div class="p-5">
        No earning histories matched your search.
        </div>
    </div>
    @endforelse

    @if ($contents->lastPage() > 1)
    <div class="mt-8 d-flex justify-content-center">
        {{ $contents->links() }}
    </div>
    @endif
@endif
