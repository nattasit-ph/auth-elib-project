@php	
$i = 0;
$get_page = 0;
@endphp
@foreach ($rewards as $item)
@php
	$i++;
	$cal_card = $i%3;
@endphp

<div class="col-md-4

@if($i == 1 && $cal_card == 1 && $rewards->currentPage() > 1)
cal-diff-300
@elseif($i == 2 && $cal_card == 2 && $rewards->currentPage() > 1)
cal-diff-150
@elseif($i == 3 && $cal_card == 0 && $rewards->currentPage() > 1)

@elseif($i == 1 && $cal_card == 1 )
cal-diff-150
@elseif($i == 3 && $cal_card == 0)
cal-plus-150
@elseif($i == 4 && $cal_card == 1)
cal-diff-300
@elseif($i == 5 && $cal_card == 2)
cal-diff-150
@elseif($i == 6 && $cal_card == 0)

@endif

">

	<!-- Card-->
	<div class="card card-sm card-reward mt-5 d-md-block shadow overflow-hidden" data-aos="fade-up">
		<!-- circle point -->
		<span  class="circle-point">
			<div class="text-point h2 text-white">{{ number_format($item->point) }} </div>
			<div class=" h5 text-white" style="line-height: 0rem;">COINS</div>
		</span>

		<!-- Image -->
		<div class="" style="height: 250px;">
			@forelse ($item->rewardGalleries as $gallery)
				@if($gallery->is_cover == 1)
				<img src="{{ Storage::url($gallery->file_path)}}" class="card-img-top" style="height: fit-content; max-width: 100%; max-height: 100%;object-fit:cover;" alt="">
				@else
				<img src="{{ asset('auth/'.config('bookdose.theme_front').'/img/goodkit/placeholder/no-image-m.png') }}" class="card-img-top" style="height: fit-content; max-width: 100%; max-height: 100%;object-fit:cover;" alt="">
				@endif
			@empty
				<img src="{{ asset('auth/'.config('bookdose.theme_front').'/img/goodkit/placeholder/no-image-m.png') }}" class="card-img-top" style="height: fit-content; max-width: 100%; max-height: 100%;object-fit:cover;" alt="">
			@endforelse
		</div>


		<!-- Body -->
		<div class="card-body text-center d-flex align-items-center flex-column" style="height: 250px;">

			<!-- Title -->
			<div class="title-reward">
				<div class="h3 px-5">
					{{ \Illuminate\Support\Str::limit($item->title, 50, '...') }}
				</div>
			</div>
			
			<!-- Link -->
			<div class="link-reward mt-auto mb-5">
				<a class="text-decoration-none btn bg-triple text-primary" href="{{route('reward.detail',$item->id)}}">Redeem Reward</a>
			</div>
		</div>
	</div>
</div>
@endforeach

<script>
	$(function() {
		AOS.init();
	});
</script>
