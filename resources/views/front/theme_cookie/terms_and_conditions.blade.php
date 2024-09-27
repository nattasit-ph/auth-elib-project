@extends('front.'.config('bookdose.theme_front').'.tpl_front', [ 'menu' => 'interest'])

@push('additional_css')
<link rel="stylesheet" href="{{ asset(config('bookdose.app.folder').'/'.config('bookdose.app.custom_css')) }}">
<style>
    .overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 2;
        cursor: pointer;
    }

    .interest-content {
        width: 680px;
    }

    @media (max-width: 991px) {
        .interest-content {
            width: 372px;
        }
    }

    @media (max-width: 576px) {
        .interest-content {
            width: auto;
            justify-content: center !important;
        }
    }
</style>
@endpush

@section('content')
<div class="bg-secondary" style="min-height: 60vh;">
    <div class="container">

        <div class="mt-5 position-relative overflow-hidden" style="height: 80%; padding-top: 30px;">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <h3 class="pt-5 text-center">{{__('common.common_terms')}}</h3>
                    <?php 
                    $policy_result = DB::table('policy_and_terms')
                             ->select('*')
                             ->where('type', '=', 2)
                             ->get();

                    $detail = "";         
                    $language = app()->getLocale();    
                    foreach ($policy_result as $row) {
                        if($language == "th"){
                            $detail = $row->detail_th;
                        }else{
                            $detail = $row->detail_en;
                        }
                    }
                    echo $detail;
                    ?>
                    <br>
                </div>
            </div>
        </div>
        @if(is_null(Auth::user()->data_interested))
        @include('front.'.config('bookdose.theme_front').'.modules.interested.model_select_interest')
        @endif
    </div>
</div>


@endsection

@push('additional_js')

@endpush