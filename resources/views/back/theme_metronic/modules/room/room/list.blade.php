@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Room')
@section('page_title', 'Room')
@section('topbar_button')
<a href="{{ route('admin.room.create') }}" class="btn btn-brand btn-bold">
	<i class="fa fa-plus"></i> Add New 
</a>
<div>
	<form id="frm_export" method="get" action="{{ route('admin.room.exportToExcel') }}">
		<input type="hidden" id="hd_status" name="hd_status" value="">
		<input type="hidden" id="hd_keyword" name="hd_keyword" value="">
		<a id="btn_export_to_excel" href="javascript:void(0);" class="btn btn-success">
			<i class="fa fa-file-excel"></i> Export to excel
		</a>
	</form>
</div>
@endsection

@push('additional_css')
@endpush

@section('content')
<div id="main_content" class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	
	{{-- Display Success Message Area --}}
	@if(session()->get('success'))
	    <div class="alert alert-solid-success alert-bold alert-dismissible fade show" role="alert" dismissable="true">
	      <div class="alert-text">{{ session()->get('success') }}</div>
	      <div class="alert-close">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="la la-close"></i></span>
				</button>
			</div>
	    </div>
  	@endif

  	{{-- Display Error Area --}}
	@if ($errors->any())
	    <div class="alert alert-solid-danger alert-bold" role="alert">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	<!-- Search  -->
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<div class="row d-flex justify-content-start">

				<div class="form-inline-block col-md-6 mb-4">
					<label class="text-dark font-pri mr-2">Room Type:</label>
					<select id="room_type_id" name="room_type_id" class="form-control custom-select">
						<option value="">All</option>
					   @foreach ($room_type as $item)
						<option value="{{ $item->id }}">{{ $item->title }} @if(!empty($item->description)) ( {{$item->description}} ) @endif</option>
					   @endforeach
				   </select>
				</div>

				<div class="form-inline-block col-md-6 mb-4">
					<label class="text-dark font-pri mr-2">Room/Space:</label>
					<select id="room_id" class="form-control custom-select">
						<option value="">All</option>
				   </select>
				</div>

				<div class="form-inline-block col-lg-3 col-md-4 mb-4">
					<label class="text-dark font-pri mr-2">Date:</label>
					<div class="input-group">
                        <input id="date_booking" value="{{ date('Y/m/d', strtotime(now()))}}" type="text" class="form-control datepicker" placeholder="yyyy/mm/dd">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="fas fa-calendar-alt"></i></button>
                        </div>
                    </div>
				</div>

				<div class="form-inline-block col-lg-3 col-md-4 mb-4">
					<label class="text-dark font-pri mr-2">From:</label>
					<select id="time_from" class="form-control custom-select">
                        <option value="">ไม่ระบุ</option>
                        @for($h=0; $h<=23; $h++)
                        @for($m=0; $m<60; $m=$m+30)
                            <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                        @endfor
                    </select>
				</div>

				<div class="form-inline-block col-lg-3 col-md-4 mb-4">
					<label class="text-dark font-pri mr-2">To:</label>
					<select id="time_to" class="form-control custom-select">
                        <option value="">ไม่ระบุ</option>
                        @for($h=0; $h<=23; $h++)
                        @for($m=0; $m<60; $m=$m+30)
                            <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                        @endfor
                    </select>
				</div>

				<div class="form-inline-block col-lg-3 col-md-12 mb-4">
					<label class="text-dark font-pri mr-2">&nbsp; </label>
					<a href="javascript:void(0);" class="btn-block btn btn-brand btn-bold" onclick="searchRoom()">Search</a>
				</div>
				{{-- <div class="form-inline-block col-1">
					<label class="text-dark font-pri mr-2">&nbsp; </label>
					<a href="javascript:void(0);" class="btn-block btn btn-danger btn-bold" onclick="Clear()">Clear</a>
				</div> --}}

			</div>
		</div>
	</div>

	<!-- Filter -->
	{{-- <div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<div class="row d-flex justify-content-start">
				<div class="form-inline-block col-4">
					<label class="text-dark font-pri mr-2">สถานะ:</label>
					<select id="ddl_filter_status" class="form-control">
						<option value="">ทั้งหมด</option>
						<option value="1">Active</option>
						<option value="0">Inactive</option>
					</select>
				</div>
			</div>
		</div>
	</div> --}}
	
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<table class="table table-hover dt-bootstrap4 no-footer" id="main-table">
			     <thead>
			         <tr>
			            <th>ID</th>
						<th class="w-25">รูปห้อง</th>
			            <th>ชื่อห้อง/สถานที่</th>
			            <th>จำนวนรองรับได้สูงสุด</th>
			            <th>สถานะ</th>
			            <th></th>
			         </tr>
			     </thead>
			 </table>
		</div>
	</div>
	<!--begin::Portlet-->
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.room.setStatus') }}">
	<input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.room.delete') }}">
	
		

	<!--end::Portlet-->
</div>
@endsection

@push('additional_js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
function initDataTable() {
	if ($.fn.DataTable.isDataTable("#main-table")) {
        $("#main-table").DataTable().clear();
        $("#main-table").dataTable().fnDestroy();
 	}
	$('#main-table').DataTable({
	     processing: true,
	     serverSide: true,
	     pageLength: 25,
	     ajax: {
            "url": "{{ route('admin.room.datatable') }}",
            "type": "POST",
            "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "data": {
            	filter_status: $('#ddl_filter_status').val(),
	     		},
	     		"dataType": 'json',
        },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
			 {data: 'file_path_html', name: 'file_path', orderable: false, searchable: false},
	         {data: 'title_action', name: 'title', orderable: true, searchable: true},
			 {data: 'max_seats_html', name: 'max_seats', orderable: true, searchable: true},
	        //  {data: 'txt_event_start', name: 'event_start', orderable: true, searchable: false},
	        //  {data: 'txt_event_end', name: 'event_end', orderable: true, searchable: false},
	         {data: 'status_html', name: 'status', orderable: true, searchable: false},

	         // {data: 'updated_date', name: 'events.updated_at'},
	         {data: 'actions', name: 'actions', orderable: false, searchable: false},
	     ],
	     order: [[ 3, "desc" ]],
	     initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}

$(document).ready(function() {
	initDataTable();

	$('#ddl_filter_status, #ddl_filter_event_type, #dp_event_start, #dp_event_end').on('change', function(e) {
		initDataTable();
		e.preventDefault();
		e.stopPropagation();
	});

	$('#dp_event_start, #dp_event_end').datepicker({
	   rtl: KTUtil.isRTL(),
	   todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   orientation: "bottom left",
	   autoclose: true,
	});
	
	$('#btn_export_to_excel').on('click', function(e) {
		e.preventDefault();
		$('#hd_keyword').val($('input[type="search"]').val());
		$('#frm_export').submit();
	});

	$.fn.datepicker.defaults.format = "yyyy/mm/dd";
	$('#date_booking').datepicker({
	   rtl: KTUtil.isRTL(),
	   todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   orientation: "bottom left",
	   autoclose: true,
	});

});

$('#room_type_id').on('change', function() {
	var room_type_id = this.value;

	$.ajax({
			url:"{{ route('admin.room.ajaxRoomName') }}",
			method: 'get',
			data : {
				room_type_id: room_type_id,
			},
			beforeSend: function() {
				$('#room_id').find('option').remove();
			},
			success:function(data)
			{	
				$('#room_id').append($('<option value="">' +'All'+ '</option>'));
				$.each(data, function( index, value ) {
					$('#room_id').append($('<option value="' +value.id+ '">' +value.title+ '</option>'));
				});  
					
			},
			complete:function(data){
				// Hide image container
				// $("#loader").hide();
			}
	});
	
});	
	function timeStringToFloat(time) {
		var hoursMinutes = time.split(/[.:]/);
		var hours = parseInt(hoursMinutes[0], 10);
		var minutes = hoursMinutes[1] ? parseInt(hoursMinutes[1], 10) : 0;
		return hours + minutes / 60;
	}
	function jsDateDiff1(strDate1,strDate2){
		var theDate1 = Date.parse(strDate1)/1000;
		var theDate2 = Date.parse(strDate2)/1000;
		var diff=(theDate2-theDate1)/(60*60*24);
		return diff;
	}


	function jsDateDiff2(strDate1,strDate2){
		date1 = new Date(strDate1);
		date2 = new Date(strDate2);
		
		var one_day = 1000*60*60*24;
		var defDate = (date2.getTime() - date1.getTime()) / one_day

		return defDate;
	}

	function GetFormattedDate() {
		var todayTime = new Date();
		var month = format(todayTime .getMonth() + 1);
		var day = format(todayTime .getDate());
		var year = format(todayTime .getFullYear());
		return month + "/" + day + "/" + year;
	}

    function searchRoom() {

		var room_id = $('#room_id').val();
		var room_type_id = $('#room_type_id').val();
		var date_booking = $('#date_booking').val();
		var time_from = $('#time_from').val();
		var time_to = $('#time_to').val();

		//get date now
		var dateObj = new Date();
		var month = dateObj.getUTCMonth() + 1; //months from 1-12
		var day = dateObj.getUTCDate();
		var year = dateObj.getUTCFullYear();
		var hours = dateObj.getHours();
		var minutes = (dateObj.getMinutes()<10?'0':'') + dateObj.getMinutes()
		date_now = year + "/" + month + "/" + day;
		time_now = hours +":"+ minutes;

			
		const date1 = new Date(date_now);
		const date2 = new Date(date_booking);
		const diffTime = Math.abs(date2 - date1);
		const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        // console.log(room_id, date_booking,time_from,time_to);
		// $('.row.room').first().fadeOut('slow');
		// $('.row.room').last().fadeOut('slow');

		if(room_type_id ==="" && room_id ==="" && date_booking ===""){
			Swal.fire(
				'พบข้อผิดพลาด',
				'โปรดเลือกห้องหรือวันที่เพื่อค้นหา',
				'error'
			);					
			return;
		}

		if(time_from !== "" || time_to !==""){
			if(date_booking === ""){
				Swal.fire(
					'พบข้อผิดพลาด',
					'โปรดเลือกวันที่ต้องการค้นหา',
					'error'
				);					
				return;
			}
		}

		if(time_from !== "" && time_to === ""){

			Swal.fire(
				'พบข้อผิดพลาด',
				'โปรดเลือกเวลาสิ้นสุด',
				'error'
			);					
			return;

		}

		if(time_to !== "" && time_from === ""){
			
			Swal.fire(
				'พบข้อผิดพลาด',
				'โปรดเลือกเวลาเริ่มต้น',
				'error'
			);					
			return;
	
		}

		var check_time_now = timeStringToFloat(time_now);
		var check_time_from = timeStringToFloat(time_from);
		var check_time_to = timeStringToFloat(time_to);
		var diff_time = check_time_to - check_time_from;
		var check_time_today  = check_time_now - check_time_from;

		if(diffDays==0){
			// console.log(check_time_today);
			if(check_time_today >=  0){
				Swal.fire(
					'พบข้อผิดพลาด',
					'เวลาจองไม่สอดคล้องกับเวลาปัจจุบัน',
					'error'
				);					
				return;
			}
		}		
		
		if(check_time_from == check_time_to){
			Swal.fire(
				'พบข้อผิดพลาด',
				'เวลาเริ่มต้นไม่สามารถเท่ากับเวลาสิ้นสุดได้',
				'error'
			);					
			return;
		}

		if(check_time_from > check_time_to){
			Swal.fire(
				'พบข้อผิดพลาด',
				'เวลาเริ่มต้นไม่สามารถมากกว่าเวลาสิ้นสุดได้',
				'error'
			);					
			return;
		}

		if ($.fn.DataTable.isDataTable("#main-table")) {
			$("#main-table").DataTable().clear();
			$("#main-table").dataTable().fnDestroy();
		}
		$('#main-table').DataTable({
			processing: true,
			serverSide: true,
			pageLength: 25,
			ajax: {
				"url": "{{ route('admin.room.datatable') }}",
				"type": "POST",
				"headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				"data": {
					room_type_id: room_type_id,
					room_id: room_id,
					date_booking: date_booking,
					time_from: time_from,
					time_to: time_to,
					},
				"dataType": 'json',
			},
			columns: [
				{data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
				{data: 'file_path_html', name: 'file_path', orderable: false, searchable: false},
				{data: 'title_action', name: 'title', orderable: true, searchable: true},
				{data: 'max_seats_html', name: 'max_seats', orderable: true, searchable: true},
				//  {data: 'txt_event_start', name: 'event_start', orderable: true, searchable: false},
				//  {data: 'txt_event_end', name: 'event_end', orderable: true, searchable: false},
				{data: 'status_html', name: 'status', orderable: true, searchable: false},

				// {data: 'updated_date', name: 'events.updated_at'},
				{data: 'actions', name: 'actions', orderable: false, searchable: false},
			],
			order: [[ 3, "desc" ]],
			initComplete: function(settings, json) {
				$('#main-table thead').addClass('bg-light');
				$('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
				$('div.dataTables_filter input').addClass('form-control form-control-sm');
			}
		});

	}

	function Clear(){
		initDataTable();
	}
</script>
@endpush
