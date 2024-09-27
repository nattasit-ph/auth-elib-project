<meta name="csrf-token" content="{{ csrf_token() }}">
@isset($meta)
<!-- Primary Meta Tags -->
<meta name="title" content="{{ $meta->title }}">
<meta name="description" content="{{ $meta->description }}">

<!-- Open Graph / Facebook -->
<meta property="og:title" content="{{ $meta->title }}" />
<meta property="og:description" content="{{ $meta->description }}" />
<meta property="og:image" content="{{getCoverImage($meta->cover_file_path, $meta->type, true)}}" onerror="this.content='<?= asset('client/'.config('bookdose.app.folder').'/images/placeholder/default-'.$meta->type.'.png') ?>'">


<!-- Twitter -->
<meta property="twitter:title" content="{{ $meta->title }}">
<meta property="twitter:description" content="{{ $meta->description }}">
<meta property="twitter:image" content="{{getCoverImage($meta->cover_file_path, $meta->type, true)}}" onerror="this.content='<?= asset('client/'.config('bookdose.app.folder').'/images/placeholder/default-'.$meta->type.'.png') ?>'">

@else
<!-- Primary Meta Tags -->
<meta name="title" content="{{__('common.default_page_title')}}">

<!-- Open Graph / Facebook -->
<meta property="og:title" content="{{__('common.default_page_title')}}" />
<meta property="og:image" content="<?= getOrgLogo() ?>" onerror="this.content='<?= asset('/front/' . config('bookdose.theme_front') . '/images/logos/logo_'.config('bookdose.app.project').'.png') ?>'">

<!-- Twitter -->
<meta property="twitter:title" content="{{__('common.default_page_title')}}">
<meta property="twitter:image" content="<?= getOrgLogo() ?>" onerror="this.content='<?= asset('/front/' . config('bookdose.theme_front') . '/images/logos/logo_'.config('bookdose.app.project').'.png') ?>'">
@endif
