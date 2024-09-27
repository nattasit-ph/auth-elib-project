
<!-- JAVASCRIPT -->
<!-- Polyfills -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=Array.prototype.find,Array.prototype.includes,Array.from,Object.entries,Promise,Object.assign"></script>

<!-- Libs goodkit JS -->

<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/@fancyapps/fancybox/dist/jquery.fancybox.min.js') }}"></script>


{{-- <script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/@popperjs/core/dist/umd/popper.min.js') }}"></script> --}}


<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/aos/dist/aos.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/countup.js/dist/countUp.min.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/flickity/dist/flickity.pkgd.min.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/flickity-fade/flickity-fade.js') }}"></script>

<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/highlightjs/highlight.pack.min.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
{{-- <script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/isotope-layout/dist/isotope.pkgd.min.js') }}"></script> --}}

<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/jarallax/dist/jarallax.min.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/jarallax/dist/jarallax-video.min.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/jarallax/dist/jarallax-element.min.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/smooth-scroll/dist/smooth-scroll.min.js') }}"></script>
<script src="{{ asset('/front/'.config('bookdose.theme_front').'/libs/typed.js/lib/typed.min.js') }}"></script>


<!-- Map -->
<script src="https://api.mapbox.com/mapbox-gl-js/v0.53.0/mapbox-gl.js"></script>

<!-- Typeahead -->
<script type="text/javascript" src="{{ asset('/front/'.config('bookdose.theme_front').'/js/goodkit/front/bloodhound.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/front/'.config('bookdose.theme_front').'/js/goodkit/front/typeahead.bundle.min.js') }}"></script>

<script type="text/javascript" src="{{ url(config('bookdose.app.folder').'/js/common.js') }}"></script>

<!-- Back to top button -->
<a id="btn_back_to_top"><i class="fas fa-arrow-up pt-4 fa-lg text-white"></i></a>

<!-- Back to top button script -->
<script>var btn = $('#btn_back_to_top');
    $(window).scroll(function() {
      if ($(window).scrollTop() > 300) {
        btn.addClass('show');
      } else {
        btn.removeClass('show');
      }
    });
    
    btn.on('click', function(e) {
      e.preventDefault();
      $('html, body').animate({scrollTop:0}, '300');
    });
</script>