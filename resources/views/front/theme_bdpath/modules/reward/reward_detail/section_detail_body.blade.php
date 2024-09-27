@push('additional_css')
<style>
/*-------------------------------------------------
    Reward Detail
-------------------------------------------------*/

.nextArrowBtn {
    position: absolute;
    z-index: 1000;
    top: 50%;
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    right: 0;
    cursor: pointer;

}

.prevArrowBtn {
    position: absolute;
    z-index: 1000;
    top: 50%;
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    left: 0;
    cursor: pointer;
}

.btn-slick img {
    width: 70px;
    border-radius: 50%;
   
}

@media (max-width: 575px) {
    .btn-slick img {
        width: 40px;
    }

}

</style>
@endpush
<section class="container-lg pb-5 pb-md-5">

    <!-- Slider -->
    <div class="slick-slider-img my-5">
        @forelse ($detail_pic as $item)
        <div class="d-flex justify-content-center align-items-center mx-3">
            <img src="{{ Storage::url($item->file_path)}}" class="img-fluid rounded" style="max-width: 80%;" alt="">
        </div>
        @empty
        <div class="d-flex justify-content-center align-items-center mx-3">
            <img src="{{ asset('auth/'.config('bookdose.theme_front').'/img/goodkit/placeholder/no-image-horizontal.png') }}" class="img-fluid  rounded" style="max-width: 80%;" alt="">
        </div>
        @endforelse
    </div>

    <!-- Item name -->
    <div class="col-md-12 text-center">
        <p class="h2 mb-3 mt-0 mt-md-3">
            <strong>{{ $detail_items->title }}</strong>
        </p>
        @if($detail_items->stock_avail <= 0) <button class="btn bg-primary disabled text-white">Out of stock</button>
            <div class="mt-3 text-center h5">ขออภัย ของรางวัลหมด กรุณา
                <a style="color:var(--bs-secondary)" href="/reward">คลิก</a> เพื่อกลับหน้าร้านค้าของรางวัล
            </div>
            @elseif($redeem_qty >= $detail_items->max_per_user && ($detail_items->max_per_user != 0 || $detail_items->max_per_user != null))
            <button class="btn bg-primary disabled text-white">Quota Exceeded</button>
            <div class="mt-3 text-center h5">คุณมาถึงขีดจำกัด {{$detail_items->max_per_user}} รายการต่อผู้ใช้แล้ว กรุณา
                <a style="color:var(--bs-secondary)" href="/reward">คลิก</a> เพื่อกลับหน้าร้านค้าของรางวัล
            </div>
            @else
            <button class="btn bg-primary text-white" data-bs-toggle="modal" data-bs-target="#modalConfirm">Redeem {{ number_format($detail_items->point,0) }} Coins</button>
            @endif
    </div>

    <!-- Item Condition -->
    <div class="pt-3 pt-md-5">
        <div class="bg-primary text-white text-left">
            <label class="py-2 h4 m-0 px-5">Condition of Redemption</label>
        </div>
        <div class="bg-triple">
            <div class="h6 pt-2 overflow-hidden">
                <div class="row py-2" style="border-bottom: 2px dotted gainsboro;">
                    <div class="col-md-4 col-5">
                        <div class="ms-3">จำนวน Coins ที่ใช้แลก</div>
                    </div>
                    <div class="col-md-8 col-7">
                        <label>{{ number_format($detail_items->point,0) }} Coins</label>
                    </div>
                </div>
                <div class="row py-2" style="border-bottom: 2px dotted gainsboro;">
                    <div class="col-md-4 col-5">
                        <div class="ms-3">จำนวนของรางวัลคงเหลือ</div>
                    </div>
                    <div class="col-md-8 col-7">
                        <label id="stock_avail">{{ $detail_items->stock_avail }}</label>
                    </div>
                </div>
                <div class="row py-2" style="border-bottom: 2px dotted gainsboro;">
                    <div class="col-md-4 col-5">
                        <div class="ms-3">จำนวนชิ้นที่สามารถแลกได้สูงสุดต่อผู้ใช้งาน</div>
                    </div>
                    <div class="col-md-8 col-7">
                        <label>{{ $detail_items->max_per_user ?? 'ไม่จำกัดจำนวนแลก'}}</label>
                    </div>
                </div>
                <div class="row py-2" style="border-bottom: 2px dotted gainsboro;">
                    <div class="col-md-4 col-5">
                        <div class="ms-3">ช่วงระยะเวลาการแลกของรางวัล</div>
                    </div>
                    <div class="col-md-8 col-7">
                        @if(empty($detail_items->started_at) && empty($detail_items->expired_at))
                        <label>จนกว่าของรางวัลจะหมด</label>
                        @elseif(empty($detail_items->started_at) && !empty($detail_items->expired_at))
                        <label>{{ 'วันนี้ - '.date('d/m/Y', strtotime($detail_items->expired_at)) }}</label>
                        @elseif(!empty($detail_items->started_at) && empty($detail_items->expired_at))
                        <label>จนกว่าของรางวัลจะหมด</label>
                        @else
                        <label>{{ date('d/m/Y', strtotime($detail_items->started_at)).' - '.date('d/m/Y', strtotime($detail_items->expired_at)) }}</label>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Detail -->
    <div class="pt-2">
        <div class="bg-primary text-white text-left">
            <label class="py-2 h4 m-0 px-5">Description</label>
        </div>
        <div class="bg-triple p-2 h6" style="text-indent: 50px;">
            {!! nl2br($detail_items->description ?? '') !!}
        </div>
    </div>
</section>