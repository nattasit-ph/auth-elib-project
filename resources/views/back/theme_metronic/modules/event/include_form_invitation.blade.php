<div class="kt-portlet__body p-5">
	<div class="row border-bottom p-4 bg-label-brand mb-4">
		<div class="col-md-10">
			<select id="ddl_invited_user" name="ddl_invited_user" class="select2 w-100">
				<option>กรุณาเลือก</option>
				@foreach ($all_users as $user) 
					<option value="{{ $user->id }}">{{ $user->username.' - '.$user->name }}</option>
				@endforeach
			</select>
			<div class="kt-checkbox-inline pt-3">
				<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand kt-checkbox--check-bold font-pri">
					<input type="checkbox" name="status" value="1" checked="" onclick="javascript:return false;"> ส่งคำเชิญทางอีเมล
					<span></span>
				</label>
			</div>
		</div>
		<div class="col-md-2">
			<button id="btn_invite" type="button" class="btn btn-brand w-100" onclick="ajaxInvite()"><i class="far fa-plus"></i> Invite</button>
		</div>
	</div>

	<table class="table table-hover dt-bootstrap4 no-footer" id="main-table">
     <thead>
         <tr>
             <th>ID</th>
             <th>Created At</th>
             <th>Username</th>
             <th>ชื่อ-สกุล</th>
             <th>วันที่ส่งคำเชิญ</th>
             <th>วันที่ลงทะเบียนเข้าร่วมกิจกรรม</th>
             <th></th>
         </tr>
     </thead>
 </table>
 <input type="hidden" class="form-control" id="page_url_delete" value="{{ route('admin.event.invitation.ajaxDeleteInvitation') }}">
</div>


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
            "url": "{{ route('admin.event.invitation.datatable') }}",
            "type": "POST",
            "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            "data": {
            	event_id: {{ $event->id }},
	     		},
	     		"dataType": 'json',
        },
	     columns: [
	         {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
	         {data: 'created_at', name: 'created_at', orderable: true, searchable: false, visible: false },
	         {data: 'username', name: 'username', orderable: true, searchable: true},
	         {data: 'name', name: 'name', orderable: true, searchable: false},
	         {data: 'invited_date', name: 'invited_at', orderable: true, searchable: false},
	         {data: 'joined_date', name: 'joined_at', orderable: true, searchable: false},
	         {data: 'actions', name: 'actions', orderable: false, searchable: false},
	     ],
	     order: [[ 1, "desc" ]],
	     initComplete: function(settings, json) {
		    $('#main-table thead').addClass('bg-light');
		    $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
		    $('div.dataTables_filter input').addClass('form-control form-control-sm');
		  }
	 });
}
function ajaxInvite() {
	$.ajax({
		url: "{{ route('admin.event.invitation.ajaxSendInvitation') }}",
		type: 'POST',
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		data: {
			event_id: {{ $event->id }},
			user_id: $('#ddl_invited_user').val(),
		},
		dataType: 'json',
		beforeSend() {
			$('#btn_invite').prop('disabled', true);
		},
	})
	.done(function(resp) {
		$('#btn_invite').prop('disabled', false);
		initDataTable();
		showNotifyOnScreen(resp);
	});
}

$(function() {
   $('.select2').select2();

   initDataTable();
})
</script>
@endpush