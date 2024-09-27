<section class="bg-breadcrumbs pl-lg-5 pl-md-4 pl-sm-3 pl-2 py-2 text-dark">
    <div class="container-lg">
        <div class="d-flex justify-content-start mb-0 p-0 lh-1">
            <div>
                <i class="ms-3 fas fa-map-marker-alt"></i> 
                <a href="{{ route('home') }}">
                    <span class="d-inline mx-1 text-dark">{{__('menu.front.gpo.home')}}</span>
                </a>
            </div>
            <div class="sub-link">
                @isset ($breadcrumbs)
                @foreach ($breadcrumbs as $key => $url)
                    <i class="fas fa-angle-right mx-1"></i>
                    @if (! $loop->last)
                        <a href="{{ url($url) }}">
                            <span class="d-inline mx-1 text-dark">{{ $key }}</span>
                        </a>
                    @else
                        <span class="d-inline mx-1">{{ $key }}</span>
                    @endif
                @endforeach
                @endisset
            </div>
        </div>
    </div>
</section>