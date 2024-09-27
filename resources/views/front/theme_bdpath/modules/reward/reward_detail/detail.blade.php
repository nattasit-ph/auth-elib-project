@extends('front.'.config('bookdose.theme_front').'.tpl_front')

@section('title', 'Reward Store'.config('bookdose.app.meta_title'))

@push('additional_css')
<!-- slick slider -->
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />

<link rel="stylesheet" href="{{ asset(config('bookdose.app.folder').'/'.config('bookdose.app.custom_css')) }}">
<link rel="stylesheet" href="{{ asset('auth/'.config('bookdose.theme_login').'/assets/css/reward.css') }}">

@endpush

@section('content')

<!-- Header Page -->
@include('front.'.config('bookdose.theme_front').'.modules.reward.reward_detail.section_detail_head')

<!-- Body Page -->
@include('front.'.config('bookdose.theme_front').'.modules.reward.reward_detail.section_detail_body')

<!--- Load more --->
<div class="justify-content-center position-fixed ajax-load" style="top: 50%; left: 50%; display: none;">
	<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
		<span class="sr-only">Loading...</span>
	</div>
</div>

<!-- Modal Comfirm -->
<div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="modalConfirmHeading" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body text-center">
				<h1 class="pt-3" id="modalConfirmHeading">Confirmation</h1>
				<p class="text-muted h5 pb-5 pt-3">ยืนยันแลก {{ number_format($detail_items->point,0) }} Coins เพื่อรับ {{ $detail_items->title }}</p>
				<div class="d-flex justify-content-center">
					<button class="btn btn-block bg-primary text-white me-4 py-2 px-4" onclick="confirm_submit()">Confirm</button>
					<button class="btn btn-block btn-cancel bg-triple text-primary py-2 px-4" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Redeem Reward Success -->
<div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog" aria-labelledby="modalSuccessHeading" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body text-center">

				<!-- Icon -->
				<div class="pb-3">
					<span class="text-primary"><i class="fas fa-check-circle fa-5x"></i></span>
				</div>

				<!-- Heading -->
				<h1 class="font-weight-bold" id="modalSuccessHeading">Congratulations</h1>

				<!-- Text -->
				<p class="text-muted pb-3 pt-3" id="Msg_OK"></p>

				<button class="btn bg-primary text-white py-2 px-4" data-bs-dismiss="modal" type="button" aria-label="Close">Done</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Redeem Reward Fail -->
<div class="modal fade" id="modalFail" tabindex="-1" role="dialog" aria-labelledby="modalSuccessHeading" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-body text-center">

				<!-- Icon -->
				<div class="pb-3">
					<span class="text-danger"><i class="far fa-times-circle fa-5x"></i></span>
				</div>

				<!-- Heading -->
				<h1 class="font-weight-bold" id="modalSuccessHeading">Oops!</h1>

				<!-- Text -->
				<p class="text-muted pb-3 pt-3" id="Msg_Fail"></p>

				<button class="btn bg-triple text-primary py-2 px-4" data-bs-dismiss="modal" type="button" aria-label="Close">Close</button>
			</div>
		</div>
	</div>
</div>
@endsection

@push ('additional_js')
<!-- slick slider -->
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.5.7/slick.min.js"></script>

<script type="text/javascript">
	$('.slick-slider-img').slick({
		dots: false,
    	infinite: true,
    	speed: 1000,
    	slidesToShow: 1,
    	slidesToScroll: 1,
		prevArrow: "<div class='prevArrowBtn my-auto mx-sm-2 mx-lg-3 mx-xl-5 btn-slick'><img src='{{ asset('auth/'.config('bookdose.theme_front').'/img/goodkit/less_than_icon.svg') }}' alt='prev' style='background-color:rgba(255,184,28,1);'/></div>",
		nextArrow: "<div class='nextArrowBtn my-auto mx-sm-2 mx-lg-3 mx-xl-5 btn-slick'><img src='{{ asset('auth/'.config('bookdose.theme_front').'/img/goodkit/more_than_icon.svg') }}' alt='next' style='background-color:rgba(255,184,28,1);'/></div>",
	
	});

	function confirm_submit($item_id) {
		$.ajax({
				url: "{{ route('reward.ajaxRedeem') }}",
				type: 'GET',
				data: {
					id: "{{ $detail_items->id }}"
				},
				beforeSend: function() {
					$('#modalConfirm').modal('toggle');
					$(".ajax-load").show();
				}
			})
			.done(function(data) {
				if (data.statusCode == 404) {
					$(".ajax-load").hide();
					$('#Msg_Fail').text(data.message);
					$('#modalFail').modal('show');
				} else if (data.statusCode == 200) {
					$(".ajax-load").hide();
					$('#Msg_OK').text('คุณมีแต้มคงเหลือ ' + data.remainPoint + ' แต้ม คุณสามารถติดต่อขอรับของรางวัลได้ที่แผนก');
					$('#point').text(data.remainPoint)
					$('#stock_avail').text(data.remainStock);
					$('#modalSuccess').modal('show');
				}
			})
			.fail(function(jqXHR, ajaxOptions, thrownError) {
				console.log('server not response');
			});
	}

	$(function() {
		$('#point').prop('Counter', 0).animate({
			Counter: parseInt('{{Auth::user()->points}}')
		}, {
			duration: 1500,
			easing: 'swing',
			step: function(now) {
				$(this).text(Math.ceil(now));
			}
		});
	});
</script>
@endpush