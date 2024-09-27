@extends('back.'.config('bookdose.theme_back').'.tpl.tpl_admin')

@section('title', 'Room Reservation')
@section('page_title', 'Room Reservation')
@section('topbar_button')
<a href="{{ route('admin.room.booking.all') }}" class="btn btn-label-brand btn-bold">
	<i class="fa fa-arrow-left"></i> Back
</a>
@endsection

@section('content')

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	{{-- Display Success Message Area --}}
	@include('back.'.config('bookdose.theme_back').'.includes.alert_success')

  	{{-- Display Error Area --}}
	@include('back.'.config('bookdose.theme_back').'.includes.alert_danger')

	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 id="section_title" class="kt-portlet__head-title">
					{{ !empty($room_list) ? 'Reservation: '.$room_list->title : 'Update Booking: '.$booking_detail->title}}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-form__actions">

           
                    @isset($room_list)
                        <button id="btn_save" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="reservation()">save</button>
                    @endisset
			

                    @isset($booking_detail)
					    <button id="btn_update" class="btn btn-brand btn-bold btn-wide kt-font-transform-u" onClick="updateReservation()">Update</button>
                    @endisset

					 <a href="{{ route('admin.room.booking.all') }}" class="ml-1 btn btn-secondary btn-bold btn-wide kt-font-transform-u">Cancel</a>
				</div>
			</div>
		</div>
		
		<div class="kt-portlet__body kt-portlet__body--fit">
			<div class="kt-grid kt-wizard-v3 kt-wizard-v3--white" id="kt_wizard_v3" data-ktwizard-state="first">
				<div class="kt-grid__item kt-grid__item--fluid kt-wizard-v3__wrapper">
					<div class="w-100">

                        <form id="frm_main" class="kt-form" action="{{ isset($booking_detail) ? route('admin.room.booking.update', $booking_detail->id) : route('admin.room.booking.store') }}" method="POST">
                         @csrf
							@include('back.'.config('bookdose.theme_back').'.modules.room.booking.section_form')
                        </form>

					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>    
@endsection
@push('additional_js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
$('#reserve_user_id').select2({
  placeholder: 'Search name or email',
  ajax: {
    url: "{{route('admin.room.booking.getuser')}}",
    dataType: "json",
    delay: 250,
    processResults: function (data) {
      return {
        results:  $.map(data, function (item) {
              return {
                  text: item.name +" - "+item.email,
                  id: item.id
              }
          })
      };
    },
    cache: true
  }
});
$(document).ready(function() {
// $('#reserve_user_id').select2();
$('#date_booking').datepicker({
	   rtl: KTUtil.isRTL(),
	   // todayBtn: "linked",
	   clearBtn: true,
	   todayHighlight: true,
	   startDate: new Date(),
	   orientation: "bottom left",
	   autoclose:true,
	   // templates: arrows
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

		function reservation() {
			var room_id = $('#room_id').val();
			var date_booking = $('#date_booking').val();
			var time_from = $('#time_from').val();
			var time_to = $('#time_to').val();
			var reserve_user_id = $('#reserve_user_id').val();
			var reserve_title  = $('#reserve_title').val();

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

			if(reserve_user_id === ""){
				Swal.fire(
					'พบข้อผิดพลาด',
					'โปรดเลือกชื่อผู้จอง',
					'error'
				);					
				return;
			}

			if(reserve_title === ""){
				Swal.fire(
					'พบข้อผิดพลาด',
					'โปรดระบุหัวข้อ',
					'error'
				);					
				return;
			}

			if(date_booking === ""){
				Swal.fire(
					'พบข้อผิดพลาด',
					'โปรดเลือกวันที่ต้องการจอง',
					'error'
				);					
				return;
			}
			// if(diffDays > 7){
			// 	Swal.fire(
			// 		'พบข้อผิดพลาด',
			// 		'สามารถจองห้องล่วงหน้าได้ไม่เกิน 7 วัน',
			// 		'error'
			// 	);					
			// 	return;
			// }

			if(time_from === "" || time_to ===""){
				Swal.fire(
					'พบข้อผิดพลาด',
					'โปรดเลือกเวลาที่ต้องการจอง',
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
					
			if(check_time_from > check_time_to){
				Swal.fire(
					'พบข้อผิดพลาด',
					'เวลาเริ่มต้นไม่สามารถมากกว่าเวลาสิ้นสุดได้',
					'error'
				);					
				return;
			}

			if(diff_time == 0){
				Swal.fire(
					'พบข้อผิดพลาด',
					'โปรดเลือกช่วงเวลาให้ถูกต้อง',
					'error'
				);					
				return;
			}

			// if(diff_time > 1){
			// 	Swal.fire(
			// 		'พบข้อผิดพลาด',
			// 		'ไม่สามารถจองเกินครั้งละ 1 ชั่วโมง',
			// 		'error'
			// 	);					
			// 	return;
			// }

            document.getElementById("frm_main").submit();
			
		}
        function updateReservation() {
            document.getElementById("frm_main").submit();
        }
</script>
@endpush