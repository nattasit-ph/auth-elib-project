<div id="main_content" class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <!-- Filter -->
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__body">
			<div class="row d-flex justify-content-start">
                <input type="hidden" name="ddl_filter_title" id="ddl_filter_title" value="{{$content->id}}">

				<div class="form-inline-block col">
					<label class="text-dark font-pri mr-2">วันที่เริ่มต้น:</label>
					<div class="input-group date">
						<input id="dp_reserve_start" name="reserve_start" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-calendar"></i>
							</span>
						</div>
					</div>
				</div>

				<div class="form-inline-block col">
					<label class="text-dark font-pri mr-2">วันที่สิ้นสุด:</label>
					<div class="input-group date">
						<input id="dp_reserve_end" name="reserve_end" type="text" class="form-control kt_datepicker required" data-date-format="dd/mm/yyyy" autocomplete="off" value="">
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-calendar"></i>
							</span>
						</div>
					</div>
				</div>


			</div>
		</div>
	</div>

    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__body">
            <table class="table table-hover dt-bootstrap4 no-footer" id="main-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="">วันที่จอง</th>
                        <th>เวลาเริ่ม</th>
                        <th>เวลาสิ้นสุด</th>
                        <th>หัวข้อ</th>
                        <th>ชื่อผู้จอง</th>
						<th>สถานะ</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
	<input type="hidden" class="form-control" id="page_url_set_status" value="{{ route('admin.room.booking.setStatus') }}">
    <input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.room.booking.delete') }}">
</div>

