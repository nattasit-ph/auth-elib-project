<!-- HEADER CONTENT -->
<section class="py-5 bg-primary position-relative overflow-hidden">

    <div class="container-lg">
        <div class="row align-items-center">
            
            <div class="col-md-6" data-aos="fade-up">
                <div class="display-2 fw-bold text-center text-md-left">
                    <span id="point">{{ number_format(Auth::user()->points ?? '0') }}</span>
                    Coins
                    <img src="{{ asset('auth/'.config('bookdose.theme_front').'/img/goodkit/reward/Point.svg') }}" alt="coin" width="50px" height="50px">
                </div>

                <div class="position-relative display-4 fw-bold text-white text-center text-md-left">
                    <div class="">Redemption & </div>
                    <div>Earnings history</div>
                </div>
               
            </div>

            <div class="col-md-6 text-center text-md-left">
                <div class=" text-center">
                    <a class="btn bg-triple text-decoration-none text-primary py-2 px-3" href="{{route('my.point')}}">My Points history <i class="fas fa-history align-middle p-1"></i></a>
                </div>
            </div>

        </div>
    </div>
</section>