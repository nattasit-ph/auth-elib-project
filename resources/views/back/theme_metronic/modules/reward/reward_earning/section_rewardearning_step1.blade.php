@section('topbar_button')
<div>
    <form id="frm_export" method="get" action="{{ route('admin.rewardEarn.exportToExcel', $org_slug) }}">
		<input type="hidden" id="hd_is_activity_type" name="hd_is_activity_type" value="">
		<input type="hidden" id="hd_is_activity" name="hd_is_activity" value="">
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
                <label class="text-dark font-th-pri mr-2">ประเภทกิจกรรม:</label>
                <select id="ddl_filter_activity_type" class="form-control">
                    <option value="">ทั้งหมด</option>
                    <option value="belib_resource">ทรัพยากร</option>
                    <option value="belib_article">บทความ</option>
                </select>
            </div>
            <div class="form-inline-block col-4">
                <label class="text-dark font-th-pri mr-2">กิจกรรมที่ทำ:</label>
                <select id="ddl_filter_activity" class="form-control" disabled="disabled">
                    <option value="">ทั้งหมด</option>
                    @foreach($resourceActivity as $value)
                    <option class="option-swap belib_resource" value="{{$value->action_name}}">{{$value->title}}</option>
                    @endforeach
                    @foreach($articleActivity as $value)
                    <option class="option-swap belib_article" value="{{$value->action_name}}">{{$value->title}}</option>
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
                    <th class="font-th-pri">วันที่ได้รับแต้ม</th>
                    <th class="font-th-pri">ผู้ได้รับแต้ม</th>
                    <th class="font-th-pri">แต้มคะแนน</th>
                    <th class="font-th-pri">ประเภทกิจกรรม</th>
                    <th class="font-th-pri">กิจกรรมที่ทำ</th>
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
                    activity_type: $('#ddl_filter_activity_type').val(),
                    activity_name: $('#ddl_filter_activity').val(),
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
                    data: 'day',
                    name: 'reward_earning_histories.created_at',
                    render: function(data, type, row) {
                        return moment(row.day).format('DD/MM/YYYY');
                    },
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'user_fullname',
                    name: 'user.name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'point',
                    name: 'reward_earning_histories.point',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'activity_type',
                    name: 'activity_type',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'activity_name',
                    name: 'rewardActivity.title',
                    orderable: true,
                    searchable: true,
                    className: 'text-center'
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

        $('#ddl_filter_activity_type').on('change', function(e) {
            if ($(this).val() != '') {
                $('#ddl_filter_activity').removeAttr('disabled');
                $('.option-swap').hide();
                $('.' + $(this).val()).show();
            } else {
                $('#ddl_filter_activity').attr("disabled", "disabled");
            }
            $('#ddl_filter_activity').val('');
            initDataTable();
            e.preventDefault();
            e.stopPropagation();
        });

        $('#ddl_filter_activity').on('change', function(e) {
            initDataTable();
            e.preventDefault();
            e.stopPropagation();
        });

        $('#btn_export_to_excel').on('click', function(e) {
			e.preventDefault();
			$('#hd_is_activity_type').val($('#ddl_filter_activity_type').val());
			$('#hd_is_activity').val($('#ddl_filter_activity').val());
			$('#hd_keyword').val($('input[type="search"]').val());
			$('#frm_export').submit();
		});

    });
</script>
@endpush
