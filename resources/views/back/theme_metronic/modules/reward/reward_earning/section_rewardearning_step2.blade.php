@section('topbar_button')
<div>
    <form id="frm_export" method="get" action="{{ route('admin.rewardEarn.exportToExcel', $org_slug) }}">
		<input type="hidden" id="hd_is_status" name="hd_is_status" value="">
		<input type="hidden" id="hd_is_role" name="hd_is_role" value="">
		<input type="hidden" id="hd_keyword" name="hd_keyword" value="">
		<input type="hidden" id="step" name="step" value="{{$step}}">
		<a id="btn_export_to_excel" href="javascript:void(0);" class="btn btn-success">
			<i class="fa fa-file-excel"></i> Export to excel
		</a>
	</form>
</div>
@endsection
<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__body">
        <div class="row d-flex justify-content-start">
            <div class="form-inline-block col-3">
                <label class="text-dark font-pri mr-2">สถานะ:</label>
                <select id="ddl_filter_status" class="form-control">
                    <option value="">ทั้งหมด</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="form-inline-block col-4">
                <label class="text-dark font-pri mr-2">สิทธิ์ผู้ใช้งาน:</label>
                <select id="ddl_filter_role" class="form-control custom-select">
                    <option value="">ทั้งหมด</option>
                    @foreach ($all_roles as $v)
                        <option value="{{ $v->id }}">
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>
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
                    <th class="font-th-pri">รหัสพนักงาน</th>
                    <th class="font-th-pri">ชื่อ-สกุล</th>
                    <th class="font-th-pri">สิทธิ์ผู้ใช้งาน</th>
                    <th class="font-th-pri">แต้มคะแนน</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@push('additional_js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.11.3/dataRender/datetime.js"></script>
<script type="text/javascript">
    function initDataTable() {
        if ($.fn.DataTable.isDataTable("#main-table")) {
            $("#main-table").DataTable().clear();
            $("#main-table").dataTable().fnDestroy();
        }
        $('#main-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("admin.rewardEarn.datatable", $org_slug) }}',
                data: {
                    filter_status: $('#ddl_filter_status').val(),
                    filter_role: $('#ddl_filter_role').val(),
                    step: $('#step').val(),
                },
                dataType: 'json',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'member_id',
                    name: 'member_id',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'user_fullname',
                    name: 'name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'user_role',
                    name: 'roles.name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'point',
                    name: 'reward_earning_histories.point',
                    orderable: true,
                    searchable: true
                }
            ],
            order: [
                [1, "desc"]
            ],
            pageLength: 25,
            initComplete: function(settings, json) {
                $('#main-table thead').addClass('bg-light');
                $('div.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
                $('div.dataTables_filter input').addClass('form-control form-control-sm');
            }
        });
    }

    $(document).ready(function() {
        initDataTable();
        $('.belib_article').hide();

        $('#ddl_filter_status').on('change', function(e) {
            initDataTable();
            e.preventDefault();
            e.stopPropagation();
        });

        $('#ddl_filter_role').on('change', function(e) {
            initDataTable();
            e.preventDefault();
            e.stopPropagation();
        });
        $('#btn_export_to_excel').on('click', function(e) {
			e.preventDefault();
			$('#hd_is_status').val($('#ddl_filter_status').val());
			$('#hd_is_role').val($('#ddl_filter_role').val());
			$('#hd_keyword').val($('input[type="search"]').val());
			$('#frm_export').submit();
		});

    });
</script>
@endpush
