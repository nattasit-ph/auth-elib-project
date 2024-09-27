@forelse ($notifications as $item)
{{-- <div class="notify-form notify-form-list cursor-pointer border-bottom" onclick='setIsReadNotification("{{ config("bookdose.app.url") }}", {{ $item->user_id }}, {{$item->id}}, "{{$system}}")'>
    <a class="row mx-0 p-3 noti-box text-dark {{ $item->is_read ? '' : 'is-read' }}"  href="{{ $item->url ? $item->url : 'javascript:;' }}">
        <div class="col-1 ps-0 pt-1 d-flex">
            <i class="fas fa-circle text-danger noti-dot {{ $item->is_read ? 'd-none' : '' }}"></i>
        </div>
        <div class="col-11 px-0 noti-box-msg">
            <div class="noti-smaller font-weight-bold">{{ $item->notification->title }}</div>
            <div class="noti-smaller">{{ $item->message }}</div>
            <div class="text-muted noti-smallest">{{ $item->created_date." ".$item->created_time }}</div>
        </div>
    </a>
</div> --}}
<div class="notify-form notify-form-list cursor-pointer border-bottom" onclick="setIsReadNotification('{{ config('bookdose.app.url') }}', {{ $item->user_id }}, {{ $item->id }}, '{{ $system }}')">
    <a class="row mx-0 p-3 noti-box text-dark is-read {{ $item->is_read ? '' : 'is-read' }}"  href="{{ $item->url ? $item->url : 'javascript:;' }}">
        <div class="d-flex fs-bigger-1 mx-2 my-2" style="line-height: 1.5rem !important; font-size: 15px;">
            <i class="fas fa-circle fas-xs text-danger mt-2 mx-2" style="font-size: 0.5rem;"></i>
            <div>
                <span class="fw-bold">{{ $item->notification->title }} </span><span class="text-danger ms-1">{{ Carbon\Carbon::parse($item->created_date)->diffForHumans(Carbon\Carbon::now()) }}</span><br>
                <span class="fs-6">{{ $item->message }} </span><br>
                <small class="text-muted">{{ Carbon\Carbon::parse($item->created_date)->isoFormat('D/MM/YYYY') }}</small>
            </div>
        </div>
    </a>
</div>
@empty
{{-- <div class="notify-form noti-small">
    <div class="row mx-0 p-3">
        No notification list
    </div>
</div> --}}
{{-- <span class="fw-bold">{{__('menu.front.no_notify_list')}}</span> --}}
<div class="notify-form">
    <div class="row text-center mx-0 p-3" style="max-height: 100vh; overflow-y:auto">
        <span class="fw-bold">{{ __('menu.front.no_notify_list') }}</span>
    </div>
</div>
@endforelse

<style>
.noti-box-msg {
    line-height: 1.5rem;
}
.noti-small {
    font-size: 0.9rem;
}
.noti-smaller {
    font-size: 0.8rem;
}
.noti-smaller.font-weight-bold {
    font-weight: bold;
    line-height: 1.5rem;
}
.noti-smallest {
    font-size: 0.7rem;
}
.noti-dot {
    font-size: 0.5rem;
}
.notify-form-list:hover{
    background-color: rgba(230, 230, 230, 0.3)
}
</style>
