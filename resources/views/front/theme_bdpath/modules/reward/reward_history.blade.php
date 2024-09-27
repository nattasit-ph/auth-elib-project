@extends('front.'.config('bookdose.theme_front').'.tpl_front')

@section('title', 'Rewards History - '.config('bookdose.app.meta_title'))

@push('additional_css')
<style type="text/css">
	.coin {
		width: 70px;
		/* box-shadow: 1px 1px 18px -1px rgba(255,255,255,0.75);
		-webkit-box-shadow: 1px 1px 18px -1px rgba(255,255,255,0.75);
		-moz-box-shadow: 1px 1px 18px -1px rgba(255,255,255,0.75); */
		filter: drop-shadow(0 0 20px #fff);
	}
	.card {
		box-shadow: none;
		color: 
	}
	@media (min-width: 992px) {
		.card-body {
			padding: 2rem;
		}
	}

	.cus-reward-mt{
		margin-top: 120px !important;
	}

	@media (max-width: 767px) {
		.cus-reward-mt{
			margin-top: 0px !important;
		}
	}
	</style>
@endpush
@section('content')

<!-- SECTION HEADEDR -->
<section class="pt-8 pt-md-11 bg-limegreen">
    <div class="container-lg position-relative">  

        <!-- Decoration 1 -->
        <div class="position-relative d-none d-md-block" style="margin-left:100%;">
            <div class="text-white mb-n12 ml-n10">
                <svg width="100" height="101" viewBox="0 0 185 186" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="2" fill="currentColor"/><circle cx="22" cy="2" r="2" fill="currentColor"/><circle cx="42" cy="2" r="2" fill="currentColor"/><circle cx="62" cy="2" r="2" fill="currentColor"/><circle cx="82" cy="2" r="2" fill="currentColor"/><circle cx="102" cy="2" r="2" fill="currentColor"/><circle cx="122" cy="2" r="2" fill="currentColor"/><circle cx="142" cy="2" r="2" fill="currentColor"/><circle cx="162" cy="2" r="2" fill="currentColor"/><circle cx="182" cy="2" r="2" fill="currentColor"/><circle cx="2" cy="22" r="2" fill="currentColor"/><circle cx="22" cy="22" r="2" fill="currentColor"/><circle cx="42" cy="22" r="2" fill="currentColor"/><circle cx="62" cy="22" r="2" fill="currentColor"/><circle cx="82" cy="22" r="2" fill="currentColor"/><circle cx="102" cy="22" r="2" fill="currentColor"/><circle cx="122" cy="22" r="2" fill="currentColor"/><circle cx="142" cy="22" r="2" fill="currentColor"/><circle cx="162" cy="22" r="2" fill="currentColor"/><circle cx="182" cy="22" r="2" fill="currentColor"/><circle cx="2" cy="42" r="2" fill="currentColor"/><circle cx="22" cy="42" r="2" fill="currentColor"/><circle cx="42" cy="42" r="2" fill="currentColor"/><circle cx="62" cy="42" r="2" fill="currentColor"/><circle cx="82" cy="42" r="2" fill="currentColor"/><circle cx="102" cy="42" r="2" fill="currentColor"/><circle cx="122" cy="42" r="2" fill="currentColor"/><circle cx="142" cy="42" r="2" fill="currentColor"/><circle cx="162" cy="42" r="2" fill="currentColor"/><circle cx="182" cy="42" r="2" fill="currentColor"/><circle cx="2" cy="62" r="2" fill="currentColor"/><circle cx="22" cy="62" r="2" fill="currentColor"/><circle cx="42" cy="62" r="2" fill="currentColor"/><circle cx="62" cy="62" r="2" fill="currentColor"/><circle cx="82" cy="62" r="2" fill="currentColor"/><circle cx="102" cy="62" r="2" fill="currentColor"/><circle cx="122" cy="62" r="2" fill="currentColor"/><circle cx="142" cy="62" r="2" fill="currentColor"/><circle cx="162" cy="62" r="2" fill="currentColor"/><circle cx="182" cy="62" r="2" fill="currentColor"/><circle cx="2" cy="82" r="2" fill="currentColor"/><circle cx="22" cy="82" r="2" fill="currentColor"/><circle cx="42" cy="82" r="2" fill="currentColor"/><circle cx="62" cy="82" r="2" fill="currentColor"/><circle cx="82" cy="82" r="2" fill="currentColor"/><circle cx="102" cy="82" r="2" fill="currentColor"/><circle cx="122" cy="82" r="2" fill="currentColor"/><circle cx="142" cy="82" r="2" fill="currentColor"/><circle cx="162" cy="82" r="2" fill="currentColor"/><circle cx="182" cy="82" r="2" fill="currentColor"/><circle cx="2" cy="102" r="2" fill="currentColor"/><circle cx="22" cy="102" r="2" fill="currentColor"/><circle cx="42" cy="102" r="2" fill="currentColor"/><circle cx="62" cy="102" r="2" fill="currentColor"/><circle cx="82" cy="102" r="2" fill="currentColor"/><circle cx="102" cy="102" r="2" fill="currentColor"/><circle cx="122" cy="102" r="2" fill="currentColor"/><circle cx="142" cy="102" r="2" fill="currentColor"/><circle cx="162" cy="102" r="2" fill="currentColor"/><circle cx="182" cy="102" r="2" fill="currentColor"/><circle cx="2" cy="122" r="2" fill="currentColor"/><circle cx="22" cy="122" r="2" fill="currentColor"/><circle cx="42" cy="122" r="2" fill="currentColor"/><circle cx="62" cy="122" r="2" fill="currentColor"/><circle cx="82" cy="122" r="2" fill="currentColor"/><circle cx="102" cy="122" r="2" fill="currentColor"/><circle cx="122" cy="122" r="2" fill="currentColor"/><circle cx="142" cy="122" r="2" fill="currentColor"/><circle cx="162" cy="122" r="2" fill="currentColor"/><circle cx="182" cy="122" r="2" fill="currentColor"/><circle cx="2" cy="142" r="2" fill="currentColor"/><circle cx="22" cy="142" r="2" fill="currentColor"/><circle cx="42" cy="142" r="2" fill="currentColor"/><circle cx="62" cy="142" r="2" fill="currentColor"/><circle cx="82" cy="142" r="2" fill="currentColor"/><circle cx="102" cy="142" r="2" fill="currentColor"/><circle cx="122" cy="142" r="2" fill="currentColor"/><circle cx="142" cy="142" r="2" fill="currentColor"/><circle cx="162" cy="142" r="2" fill="currentColor"/><circle cx="182" cy="142" r="2" fill="currentColor"/><circle cx="2" cy="162" r="2" fill="currentColor"/><circle cx="22" cy="162" r="2" fill="currentColor"/><circle cx="42" cy="162" r="2" fill="currentColor"/><circle cx="62" cy="162" r="2" fill="currentColor"/><circle cx="82" cy="162" r="2" fill="currentColor"/><circle cx="102" cy="162" r="2" fill="currentColor"/><circle cx="122" cy="162" r="2" fill="currentColor"/><circle cx="142" cy="162" r="2" fill="currentColor"/><circle cx="162" cy="162" r="2" fill="currentColor"/><circle cx="182" cy="162" r="2" fill="currentColor"/><circle cx="2" cy="182" r="2" fill="currentColor"/><circle cx="22" cy="182" r="2" fill="currentColor"/><circle cx="42" cy="182" r="2" fill="currentColor"/><circle cx="62" cy="182" r="2" fill="currentColor"/><circle cx="82" cy="182" r="2" fill="currentColor"/><circle cx="102" cy="182" r="2" fill="currentColor"/><circle cx="122" cy="182" r="2" fill="currentColor"/><circle cx="142" cy="182" r="2" fill="currentColor"/><circle cx="162" cy="182" r="2" fill="currentColor"/><circle cx="182" cy="182" r="2" fill="currentColor"/></svg>
            </div>
        </div>

        <div class="row mt-n6 align-items-center justify-content-center">
            <div class="col-md-10 col-lg-8 text-center">
                {{-- <!-- Preheading -->
                <h6 class="text-uppercase text-dark mb-5">
                    Let's explore in
                </h6> --}}

                <!-- Heading -->
                <h1 class="display-3 mb-4 text-white">My Coins History</h1>
				<div class="d-flex justify-content-center align-items-center">
					<h1 class="display-4 mb-4">{{ number_format(Auth::user()->points ?? '0') }} Coins</h1>
					<img class="ml-5 coin py-5" src="{{ asset('front/'.config('bookdose.theme_front').'/images/coins-shadow.svg') }}" alt="" >
				</div>

                <!-- BREADCRUMB -->
                <div class="os-init aos-animate d-flex justify-content-center mb-5" data-aos="fade-up" data-aos-delay="100">
                    <nav aria-label="breadcrumb" style="width: fit-content">
                        <ol class="breadcrumb breadcrumb-dark mb-0">
                        <li class="breadcrumb-item"><i class="fas fa-map-marker-alt text-white mr-2 mt-1"></i><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reward.index') }}">Reward</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Coin History</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- SHAPE -->
<div class="position-relative d-none d-md-block">
    <div class="shape shape-fluid-x shape-bottom text-limegreen">
        <div class="shape-img pb-9 pb-md-13">
            <svg viewBox="0 0 100 50" preserveAspectRatio="none"><path d="M0 0h100v25H75L25 50H0z" fill="#84BD00" class="font-reward"/></svg>
        </div>
    </div>
</div>

<!-- CONTENT -->
<section class="py-9 py-md-5 cus-reward-mt">
	<div class="container-lg">
	<div class="row justify-content-center">
		<div class="col">
			<ul class="nav nav-tabs mb-5 justify-content-center">
				<li class="nav-item">
					<a id="tab_nav_link_redemption" class="nav-link font-weight-bolder active" data-toggle="tab" href="#redemption"><h3>Redemption History</h3></a>
				</li>
				<li class="nav-item">
					<a id="tab_nav_link_earning" class="nav-link font-weight-bolder" data-toggle="tab" href="#earning"><h3>Earning History</h3></a>
				</li>
			</ul>

			<div class="tab-content">
				<!-- Redemption Tab -->
				<div id="redemption" class="tab-pane fade show active">
					{{-- @include('front.'.config('bookdose.theme_front').'.modules.reward.box_redemption') --}}
				</div>

				<!-- Earning Tab -->
				<div id="earning" class="tab-pane fade">
					{{-- @include('front.'.config('bookdose.theme_front').'.modules.reward.box_redemption') --}}
				</div>

			</div>
		</div>
	</div>

	</div>
</section>

<!-- ui-dialog for set rating -->
@include('front.'.config('bookdose.theme_front').'.modules.knowledge.modal_rating')
@endsection

@push('additional_js')
<script src="/js/front/knowledge/rating.js"></script>
<script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property={{ env("SHARE_KEY") }}&product=sop' async='async'></script>

<script>
$(document).ready(function() {

	ajaxRedemptionPagination(window.location.hash, getParameterUrlGet('page'));
	ajaxEarningPagination(window.location.hash, getParameterUrlGet('page'));

	$(document).on('click', '.pagination a', function(event) {
		event.preventDefault();
		new_url = $(this).attr('href');
		// set url on topbar of browser, it not refresh page
		window.history.pushState({ path: new_url }, '', new_url);

		if(window.location.hash == "" || window.location.hash == "#redemption") {
			ajaxRedemptionPagination(window.location.hash, getParameterUrlGet('page'));
		} else {
			ajaxEarningPagination(window.location.hash, getParameterUrlGet('page'));
		}
	});
	
	// Go to focus tab
	if (window.location.hash) {
		if (window.location.hash == '#redemption') {
			$('#tab_nav_link_redemption').trigger('click');
		}
		else if (window.location.hash == '#qearning') {
			$('#tab_nav_link_earning').trigger('click');
		}
	}

	// Add hash on url
	$('a[href*="#"]:not([href="#"])').on('click', function(event) {
		// Prevent default anchor handling (which causes the page-jumping)
		event.preventDefault();
		var target = $(this.hash);
		if (target.length) {
			window.location.hash = this.hash;
		}
	});
});

function ajaxRedemptionPagination(content_type, page)
{
	if(page === null) {
		page = 1;
	} else if(page <= 0) {
		return;
	}
	
	content_type = "#redemption";

	$.ajax({
		url: "{{ route('reward.redemption.index') }}",
		type: 'GET',
		data: {
			// for set hash url on controller, set html render, scroll bar to top tab
			content_type: content_type,
			// for query data
			page: page
		},
		dataType: 'json',
		beforeSend() {
			$(content_type).html('<div class="py-5 loading text-center">Loading...</div>');
		},
	})
	.fail(function(jqXHR, textStatus) {
		$(content_type).find('.loading').remove();
		$(content_type).append('<div class="p-5 text-center">Session expired. Please&nbsp;<a href="/login">log in</a>&nbsp;again.</div>');
	})
	.done(function(resp) {
		if (resp.status == '200') {
			$(content_type).find('.loading').remove();
			$(content_type).html(resp.html);

			// Set ui, css pagination Previous 1 2 Next
			setPaginationLink();
			
			// Go to focus tab
			var top = $(content_type).offset().top;
			document.body.scrollTop = top;
			document.documentElement.scrollTop = top;
		}
	});
}

function ajaxEarningPagination(content_type, page)
{
	if(page === null) {
		page = 1;
	} else if(page <= 0) {
		return;
	}
	
	content_type = "#earning";

	$.ajax({
		url: "{{ route('reward.earning.index') }}",
		type: 'GET',
		data: {
			// for set hash url on controller, set html render, scroll bar to top tab
			content_type: content_type,
			// for query data
			page: page
		},
		dataType: 'json',
		beforeSend() {
			$(content_type).html('<div class="py-5 loading text-center">Loading...</div>');
		},
	})
	.fail(function(jqXHR, textStatus) {
		$(content_type).find('.loading').remove();
		$(content_type).append('<div class="p-5 text-center">Session expired. Please&nbsp;<a href="/login">log in</a>&nbsp;again.</div>');
	})
	.done(function(resp) {
		if (resp.status == '200') {
			$(content_type).find('.loading').remove();
			$(content_type).html(resp.html);

			// Set ui, css pagination Previous 1 2 Next
			setPaginationLink();
			
			// Go to focus tab
			var top = $(content_type).offset().top;
			document.body.scrollTop = top;
			document.documentElement.scrollTop = top;
		}
	});
}
</script>
@endpush