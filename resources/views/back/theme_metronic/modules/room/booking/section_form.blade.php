<input type="hidden" id="room_id" name="room_id" value="{{$room_list->id ?? ''}}">
<input type="hidden" id="booking_id" name="booking_id" value="{{$booking_detail->id ?? ''}}">
<div class="kt-portlet">
    <div class="kt-portlet__body kt-portlet__body--fit">
        <div class="kt-portlet__body">
            
            <div class="form-group  col-12 col-md-12">
                <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>ผู้จอง:</label>
                

                @if(!empty($booking_detail->user_name))
                    <input name="reserve_user_id" type="text" class="form-control" placeholder="Enter title" value="{{$booking_detail->user_name}}" autocomplete="off" disabled>
                @else
                    <!-- select name from db -->
                    <select id="reserve_user_id" name="reserve_user_id" class="form-control"></select>
                    {{-- <select id="reserve_user_id" name="reserve_user_id" class="form-control">
                        <option value="">โปรดเลือก</option>
                        @foreach ($user_list as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select> --}}
                @endif
            </div>

            <div class="form-group  col-12 col-md-12">
                <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>หัวข้อเรื่อง:</label>
                <input id="reserve_title" name="reserve_title" type="text" class="form-control required" placeholder="Enter title" value="{{$booking_detail->title ?? '' }}" autocomplete="off" required>
            </div>

            <div class="form-group col-6 col-md-6">
                <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>วันที่จอง:</label>

                @if(!empty($booking_detail->user_name))
                <input  name="date_booking" type="text" class="form-control required"  value="{{ date('Y-m-d',strtotime($booking_detail->start_datetime)) ?? '' }}" autocomplete="off" disabled>
                @else
                <div class="input-group date">
                    <input id="date_booking" name="date_booking" value="{{ $booking_data['date_booking'] ? date('Y/m/d',strtotime($booking_data['date_booking'])) : date('Y/m/d',strtotime(now())) }}" type="text" class="form-control kt_datepicker" data-date-format="yyyy/mm/dd" placeholder="yyyy/mm/dd" autocomplete="off" value="">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar"></i>
                        </span>
                    </div>
                </div>
                @endif

            </div>

            @if(!empty($booking_detail->start_datetime))

                <div class="row">
                    <div class="form-group col-6 col-md-6">
                        <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>From:</label>
                        <input  name="time_from" type="text" class="form-control"  value="{{ date('H:i',strtotime($booking_detail->start_datetime)) ?? '' }}" autocomplete="off" disabled>
                    </div>
                    <div class="form-group col-6 col-md-6">
                        <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>To:</label>
                        <input  name="time_to" type="text" class="form-control"  value="{{ date('H:i',strtotime($booking_detail->end_datetime)) ?? '' }}" autocomplete="off" disabled>
                    </div>
                </div>

            @else

                @if(empty($room_list->open_time) || empty($room_list->closed_time))
                    <div class="row">
                        <div class="form-group col-6 col-md-6">
                            <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>From:</label>
                            <select id="time_from" name="time_from" class="form-control custom-select">
                                <option value="{{ $booking_data['time_from'] ?? ''}}">{{ $booking_data['time_from'] ?? 'โปรดเลือก'}}</option>
                                @for($h=0; $h<=23; $h++)
                                @for($m=0; $m<60; $m=$m+30)
                                    <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                                @endfor
                            </select>
                        </div>
                        <div class="form-group col-6 col-md-6">
                            <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>To:</label>
                            <select id="time_to" name="time_to" class="form-control custom-select">
                                <option value="{{ $booking_data['time_to'] ?? ''}}">{{ $booking_data['time_to'] ?? 'โปรดเลือก'}}</option>
                                @for($h=0; $h<=23; $h++)
                                @for($m=0; $m<60; $m=$m+30)
                                    <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                                @endfor
                            </select>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="form-group col-6 col-md-6">
                            <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>From:</label>
                            <?php 
                            $open_time_h =  date("H", strtotime($room_list->open_time)); 
                            $closed_time_h =  date("H", strtotime($room_list->closed_time));
                            $open_time_i =  date("i", strtotime($room_list->open_time));
                            $closed_time_i =  date("i", strtotime($room_list->closed_time));
                            $closed_time = $room_list->closed_time;
                            ?>
                            <select id="time_from" name="time_from" class="form-control custom-select">
                                <option value="{{ $booking_data['time_from'] ?? ''}}">{{ $booking_data['time_from'] ?? 'โปรดเลือก'}}</option>
                                @for($h=$open_time_h; $h<=$closed_time_h; $h++)
                                @for($m=0; $m<60; $m=$m+30)
                                    <?php $data_time_from = str_pad($h, 2, '0', STR_PAD_LEFT).':'.str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                                    @if($data_time_from <= $closed_time)
                                    <option value="{{ $data_time_from }}">{{ $data_time_from }}</option>
                                    @endif
                                @endfor
                                @endfor
                            </select>
                        </div>
                        <div class="form-group col-6 col-md-6">
                            <label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>To:</label>
                            <select id="time_to" name="time_to" class="form-control custom-select">
                                <option value="{{ $booking_data['time_to'] ?? ''}}">{{ $booking_data['time_to'] ?? 'โปรดเลือก'}}</option>
                                @for($h=$open_time_h; $h<=$closed_time_h; $h++)
                                @for($m=0; $m<60; $m=$m+30)
                                    <?php $data_time_to = str_pad($h, 2, '0', STR_PAD_LEFT).':'.str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                                    @if($data_time_to <= $closed_time)
                                    <option value="{{ $data_time_to }}">{{ $data_time_to }}</option>
                                    @endif
                                @endfor
                                @endfor
                            </select>
                        </div>
                    </div>
                @endif
                
            @endif
        </div>
    </div>
</div>


