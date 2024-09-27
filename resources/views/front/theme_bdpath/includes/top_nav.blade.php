@php
$topbar_nav = [];
if (NULL !== session('topbar_nav')){
    $topbar_nav = session('topbar_nav');
}
@endphp

@auth
<!-- NOTIFICATION -->
<div class="notify-list" style="right: -390px; transition: right 0.2s linear 0s;">
    <!-- Header -->
    <div class="bg-secondary p-3 d-flex justify-content-between">
        <label class="font-weight-bold m-0">{{__('menu.front.notifications')}}</label>
        <i class="fas fa-times float-right cursor-pointer" onclick="setIsReadNotification('{{ config('bookdose.sso.auth_url') }}', {{ Auth::user()->id }}, true)"></i>
    </div>
    <!-- Body -->
    <div class="font-size-small" style="height: 95vh; overflow-y: auto;">
        <!-- Notification form -->
    <div id="notify-list-body">
        <div class="notify-form">
            <div class="row mx-0 p-3" style="max-height: 100vh; overflow-y:auto">
                <span class="fw-bold">{{__('menu.front.no_notifications_list')}}</span>
            </div>
        </div>
    </div>
    <div class="py-2 cursor-pointer text-center notify-status bg-light noti-smaller font-weight-bold" onclick="ajaxNotificationPagination('{{ config('bookdose.sso.auth_url') }}', {{ Auth::user()->id }}, false, true)" style="display: none;">Load more...</div>
    </div>
</div>
@endauth

<!-- NAVBAR MAIN -->
<nav class="navbar navbar-expand-lg  bg-white">

    <!-- nav content -->
    <div class="d-flex justify-content-between py-lg-3" style="flex-wrap: wrap;">

        <!-- logo -->
        <a class="navbar-brand mx-xl-3 ps-3 my-auto" href="{{ route('home', Auth::user()->org->slug) }}">
            <img src="{{ asset('/auth/'.config('bookdose.theme_front').'/img/goodkit/logo.svg') }}">
        </a>

        <!-- content show on mobile -->
        <div class="d-flex flex-row">

            @auth
            <!-- notificati on -->
            <div class="nav-item position-relative d-flex align-items-center mx-2 d-flex d-lg-none">
                <a class="nav-link px-0" onclick="showNotify()" style="line-height: initial;">
                    <span class="fe fe-bell notify-icon text-dark"></span>
                    @if(count(Auth::user()->shelfExpire)> 0)
                    <span class="text-white bg-danger position-absolute rounded-circle text-center notify-circle notify-number-mobile">{{count(Auth::user()->shelfExpire)}}</span>
                    @endif
                </a>
            </div>
            @endauth

            <!-- toggler button -->
            <button class="navbar-toggler float-right border-0 my-3 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <div id="toggler-icon-animation">
                    <span class="bg-dark"></span>
                    <span class="bg-dark"></span>
                    <span class="bg-dark"></span>
                    <span class="bg-dark"></span>
                </div>
            </button>
        </div>

        <!-- content show on lg size -->
        <div class="collapse navbar-collapse pb-lg-0" id="nav">


            <ul class="navbar-nav w-100 d-flex justify-content-start">

                <!-- HOME -->
                <li class="nav-item d-flex align-items-center mx-xl-1 border-nav-top border-nav-bottom py-1 py-lg-0 pr-3 nav-left">
                    <a class="nav-link px-0 underline-animation ms-3 ms-lg-0" href="{{ route('home', Auth::user()->org->slug) }}"><span class="text-dark">{{__('menu.front.demo.home')}}</span></a>
                </li>

                <!-- Resources -->
                <li class="dropdown d-flex align-items-lg-center flex-column flex-lg-row border-nav-bottom py-1 py-lg-0 pr-3">
                    <a class="nav-link underline-animation ms-3 ms-lg-0" href="#" id="resourcesDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-dark">{{__('menu.front.resources')}}</span>
                    </a>

                    <div class="dropdown-menu mx-2 bg-white animate slideIn resources-dropdown" aria-labelledby="resourcesDropdown">
                        <div class="row d-flex justify-content-center align-items-center">
                            <div class="col col-md-6 d-none d-sm-block">
                                <img src="{{ asset('/auth/'.config('bookdose.theme_front').'/img/goodkit/header/Resources-dropdown-photo.png') }}" class="img-fluid ps-3" alt="">
                            </div>
                            <div class="col col-md-6 px-4">
                              <div class="row">
                                <div class="col-12 col-md-6">
                                    <span class="text-primary fw-600">{{__('menu.front.digital-resources')}}</span><br>
                                    @foreach ($topbar_nav as $value)
                                        @if($value->is_digital == 1)
                                        <a href="{{ url($org_slug.'/belib/'.$value->slug.'/all') }}"><div class="text-dark">{{  app()->getLocale() == "en" ? $value->name_en : $value->name_th }}</div></a>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="col-12 col-md-6">
                                    <span class="text-primary fw-600">{{__('menu.front.printed-resources')}}</span><br>
                                    @foreach ($topbar_nav as $value)
                                        @if($value->is_digital == 0)
                                        <a href="{{ url($org_slug.'/belib/'.$value->slug.'/all') }}"><div class="text-dark">{{  app()->getLocale() == "en" ? $value->name_en : $value->name_th }}</div></a>
                                        @endif
                                    @endforeach
                                </div>

                              </div>
                            </div>
                        </div>
                    </div>
                </li>



                <!-- NEWS -->
                <li class="nav-item d-flex align-items-center mx-xl-1 border-nav-top border-nav-bottom py-1 py-lg-0 pr-3">
                    <a class="nav-link px-0 underline-animation ms-3 ms-lg-0" href="{{ url($org_slug.'/belib/article/all') }}"><span class="text-dark">{{__('menu.front.news')}}</span></a>
                </li>
            </ul>

            <ul class="navbar-nav w-100 d-flex justify-content-end" >

                <!-- Profile -->
                @guest
                <li class="nav-item active d-flex align-items-center pl-3 pl-lg-0 py-1 py-lg-0 nav-right">
                    <a class="nav-link ms-3 ms-lg-0 underline-animation" href="{{ route('login', Auth::user()->org->slug) }}">
                        <img class="user-icon mr-1" style="margin-top: -5px;" src="{{ asset('/front/'.config('bookdose.theme_front').'/img/goodkit/user.svg') }}" alt="user" />
                        <span class="text-dark">{{__('menu.front.login')}}</span>
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                @endguest
                @auth
                <li class="dropdown d-flex align-items-lg-center flex-column flex-lg-row border-nav-bottom py-1 py-lg-0 nav-right">
                    <a class="nav-link  underline-animation ms-3 ms-lg-0" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <span class="text-dark pr-1">{{ Str::limit(Auth::user()->name,7) }}</span>
                            {!!getAvatarImage(Auth::user()->avatar_path, '', false, 'rounded-circle', 'width: 30px; height: 30px;')!!}
                        </div>
                    </a>

                    <div class="dropdown-menu mx-2 bg-white animate slideIn profile-dropdown" aria-labelledby="profileDropdown">
                        <div class="row d-flex justify-content-center align-items-center">
                            <div class="col col-md-4 d-none d-sm-block">
                                <img src="{{ asset('/front/'.config('bookdose.theme_front').'/img/goodkit/header/Profile-dropdown-photo.png') }}" class="img-fluid ps-3" alt="">
                            </div>
                            <div class="col col-md-8 px-4">
                                <div class="row">
                                    <div class="col-12 col-md-5">
                                        <span class="text-primary fw-600">Belib E-Library</span><br>
                                        <a href="{{ route('belib.my.shelf', [Auth::user()->org->slug, 'ebook']) }}"><span class="text-dark">{{__('menu.front.shelf')}}</span></a><br>
                                        <a href="{{ route('belib.my.shelf', [Auth::user()->org->slug, 'ebook']) }}"><span class="text-dark">{{__('menu.front.reserve-list')}}</span></a><br>
                                        <a href="{{ route('belib.my.shelf', [Auth::user()->org->slug, 'ebook']) }}"><span class="text-dark">{{__('menu.front.borrow-list')}}</span></a><br>
                                        <a href="{{ route('belib.my.shelf', [Auth::user()->org->slug, 'ebook']) }}"><span class="text-dark">{{__('menu.front.return-list')}}</span></a><br>
                                        @if (accessBackend())
                                        <a href="{{ route('admin.reward.index', Auth::user()->org->slug) }}"><span class="text-dark">{{__('menu.front.dashboard')}}</span></a><br>
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-5">
                                        <span class="text-primary fw-600">Personal Info</span><br>
                                        <a href="{{ route('belib.my.profile', Auth::user()->org->slug) }}"><span class="text-dark">{{__('menu.front.user-info')}}</span></a><br>
                                        {{-- <a href="{{ route('belib.my.point', Auth::user()->org->slug) }}"><span class="text-dark">{{__('menu.front.reward-history')}}</span></span></a><br> --}}
                                        <a href="javascript:void(0);" onclick="logout()"><span class="text-dark">{{__('menu.front.log_out')}}</span></a>
                                        <form class="btn-logout" action="{{ route('logout', Auth::user()->org->slug) }}" method="POST">@csrf</form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endauth

                 <!-- Reward -->
                {{-- @auth
                <li class="dropdown d-flex align-items-lg-center flex-column flex-lg-row border-nav-bottom py-1 py-lg-0 nav-right">
                <a class="nav-link underline-animation ms-3 ms-lg-0" href="{{ config('bookdose.app.auth_url').'/reward'}}">
                    <div class="d-flex align-items-center">
                        <span class="text-secondary fw-bold pr-1">{{ number_format(Auth::user()->points ?? '0') }}</span>
                        <img src="{{ asset('auth/'.config('bookdose.theme_front').'/img/goodkit/reward/Point-icon-header.svg') }}" class="rounded-circle mr-1">
                    </div>
                </a>
                @endauth --}}

                <!-- search nav lg-->
                <li class="nav-item position-relative d-none d-lg-flex pr-1 nav-right">
                    <a class="d-flex align-items-center text-dark my-auto" href="javascript:;">
                        <i class="fe fe-search bg-white px-0" id="btn-search"></i>
                    </a>
                    <div class="form-group has-search position-absolute" id="text-search" style="display: none;">
                        <span class="fe fe-search form-control-feedback text-secondary"></span>
                        <input id="input-search" type="text" class="form-control bg-light" placeholder="Search and then press enter" style="width: 300px;">
                    </div>
                </li>

                <!-- search nav modile -->
                <li class="dropdown d-flex d-lg-none align-items-lg-center flex-column border-nav-bottom flex-lg-row py-1 py-lg-0">
                    <a class="nav-link dropdown-toggle pl-3 pl-lg-0 ms-3" href="#" id="searchDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fe fe-search bg-white px-0" id="btn-search"></i>
                        SEARCH
                    </a>
                    <div class="dropdown-menu mx-2 animate slideIn border-0" aria-labelledby="searchDropdown">
                        <div class="position-relative">
                            <input type="text" id="input-search-mobile" class="form-control bg-light" placeholder="Search with file name, Keyword or tag" width="100%">
                            <i class="fe fe-search position-absolute text-secondary" style="top:12px; right:10px"></i>
                        </div>
                    </div>
                </li>

                {{-- <!-- login nav -->
                @guest
                <li class="nav-item active d-flex align-items-center pl-3 pl-lg-0 py-1 py-lg-0">
                    <a class="nav-link ms-3 ms-lg-0" href="{{ route('login') }}">
                        <img class="user-icon mr-1" style="margin-top: -5px;" src="{{ asset('/front/'.config('bookdose.theme_front').'/img/user.svg') }}" alt="user" />
                        Sign In / Register
                        <span class="sr-only">(current)</span>
                    </a>
                </li>
                @endguest
                @auth
                <li class="dropdown d-flex align-items-lg-center flex-column flex-lg-row py-1 py-lg-0">
                    <a class="nav-link dropdown-toggle pl-3 pl-lg-0 ms-3 ms-lg-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="user-icon mr-1" style="margin-top: -5px;" src="{{ asset('/front/'.config('bookdose.theme_front').'/img/user.svg') }}" alt="user" />
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu mx-2 bg-secondary animate slideIn" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item text-white" href="{{ route('admin.report.dashboard', Auth::user()->org->slug) }}">Go to dashboard</a>
                        <a class="dropdown-item text-white" href="{{ route('my.profile', Auth::user()->org->slug) }}">My Profile</a>
                        <a class="dropdown-item text-white" href="{{ route('my.shelf', Auth::user()->org->slug) }}">My Shelf</a>
                        <a class="dropdown-item text-white" href="javascript:void(0);" onclick="logout()">Log out</a>
                    </div>
                    <form class="btn-logout" action="{{ route('logout', Auth::user()->org->slug) }}" method="POST">@csrf</form>
                </li>
                @endauth --}}

                @auth
                <!-- notification -->
                <li class="nav-item position-relative align-items-center d-none d-lg-flex nav-right" style="padding-left: 5px;">
                    <a class="nav-link px-0" type="button" href="#" onclick="showNotify()" style="line-height: initial;">
                        <span class="fe fe-bell notify-icon text-dark"></span>
                        @if(count(Auth::user()->shelfExpire)> 0)
                        <span class="text-white bg-danger position-absolute rounded-circle text-center notify-circle notify-number">{{count(Auth::user()->shelfExpire)}}</span>
                        @endif
                    </a>
                </li>
                @endauth

                <!-- lang select -->
                <li class="nav-item position-relative mx-auto mx-lg-2 py-2 py-lg-0 mt-2 nav-right">
                    @if(app()->getLocale() == 'en')
                    <a href="javascript:;" onclick="switchLang('th')">
                        <img src="{{ asset('auth/'.config('bookdose.theme_front').'/img/goodkit/flag/en.png') }}" style="width:18px;">
                    </a>
                    @else
                    <a href="javascript:;" onclick="switchLang('en')">
                        <img src="{{ asset('auth/'.config('bookdose.theme_front').'/img/goodkit/flag/th.png') }}" style="width:18px;">
                    </a>
                    @endif
                    {{-- <button class="btn-{{ app()->getLocale() == 'en' ? 'primary active' : 'light' }} mt-2 border-0 rounded" onclick="switchLang('en')">EN</button>
                    <button class="btn-{{ app()->getLocale() == 'th' ? 'primary active' : 'light' }} mt-2 border-0 rounded" onclick="switchLang('th')">TH</button> --}}
                </li>

                <li class="nav-item position-relative d-flex align-items-center mx-auto mx-lg-2 pe-2 py-lg-0 nav-right nav-last-right">
                    <a href="{{ config('bookdose.app.learnext_url') }}" class="btn btn-primary btn-sm d-none" style="border-radius: 2rem 2rem 0 2rem; font-size: 1.2rem; padding: 0.2rem 1rem;">Learnext</a>
                    <a href="{{ config('bookdose.app.km_url') }}" class="btn btn-primary btn-sm d-none" style="border-radius: 2rem 2rem 0 2rem; font-size: 1.2rem; padding: 0.2rem 1rem; margin-left: 1rem;">Knowledge Center</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

@push ('additional_js')
@auth
<script src="{{ url($org_slug.'/'.config('bookdose.app.folder').'/js/notification.js') }}"></script>
<script>
    //when click outer notification panel => set all is_read = 1
    $(document).click(function(e) {
        if($(e.target).closest('.notify-list').length == 0 && $('.notify-list').css('right').localeCompare('0px') == 0) {
            setIsReadNotification("{{ config('bookdose.app.url') }}", {{ Auth::user()->id }}, 'all');
        }
    });

    getCountNotification("{{ config('bookdose.app.url') }}", {{ Auth::user()->id ?? '' }});
</script>
@endauth
<script type="text/javascript">
    function logout() {
        $('.btn-logout').submit();
    }

    function switchLang(lang) {
        window.location = "{{ url($org_slug.'/locale') }}/" + lang;
    }
    //toggle manu
    $('#toggler-icon-animation').click(function() {
        $(this).toggleClass('open');
    });

    //show search text
    $('#btn-search').click(function() {
        $('#text-search').fadeIn();
        $('#input-search').focus();
    });

    //hide search text when click out side
    $(document).click(function() {
        var textSearch = $("#text-search");
        var iconSearch = $("#btn-search");
        if (!textSearch.is(event.target) && !textSearch.has(event.target).length && !iconSearch.is(event.target) && !iconSearch.has(event.target).length) {
            textSearch.hide();
        }
        $('#btn-search').removeClass('btn-primary');
    });

    //enter in search box
    $('#input-search').keypress(function(e) {
        if (e.which == 13) {
            if ($('#input-search').val().length >= 2) {
                openSearchPage();
            }else{
                alert("Please input more 2 charecter.")
                return true;
            }
        }
    });

    //enter in search box => mobile
    $('#input-search-mobile').keypress(function(e) {
        if (e.which == 13) {
            if ($('#input-search-mobile').val().length >= 2) {
                openSearchPageMobile();
            }else{
                alert("Please input more 2 charecter.")
                return true;
            }
        }
    });

    //redirect search page => mobile
    function openSearchPageMobile() {
        window.location.replace("{{ route('belib.search', Auth::user()->org->slug) }}?word=" + $('#input-search-mobile').val());
    }


    //redirect search page
    function openSearchPage() {
        window.location.replace("{{ route('belib.search', Auth::user()->org->slug) }}?word=" + $('#input-search').val());
    }
</script>
@endpush
