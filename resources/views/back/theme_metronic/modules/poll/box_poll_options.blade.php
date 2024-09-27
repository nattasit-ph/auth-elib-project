<div class="kt-portlet">
	<div class="kt-portlet__head bg-info">
		<div class="kt-portlet__head-label">
			<h3 id="section_title" class="kt-portlet__head-title font-pri text-white">
				<i class="fas fa-vote-yea mr-2"></i> ตัวเลือกคำตอบ
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body">
		<div id="panel_options">
			<div class="form-group row-option align-items-center tpl-option d-none">
				<input type="hidden" name="poll_option_id[]" class="form-control"/>
				<input type="text" name="poll_option[]" class="form-control w-75" value=""/>
				<a href="javascript:;" class="ml-3 text-danger btn-delete-option" onclick="removeOptionRow(this)"><i class="fas fa-times mr-1"></i> Delete</a>
			</div>
			@isset($poll_options)
			@if ($poll_options->count() > 0)
				@foreach ($poll_options as $k=>$opt)
					<div class="form-group row-option d-flex align-items-center">
						<input type="hidden" name="poll_option_id[]" value="{{ $opt->id }}" class="form-control"/>
						<input type="text" name="poll_option[]" class="form-control w-75" value="{{ $opt->title }}"/>
						@if ($poll->total_votes == 0)
							<a href="javascript:;" class="ml-3 text-danger btn-delete-option" onclick="removeOptionRow(this)"><i class="fas fa-times mr-1"></i> Delete</a>
						@endif
					</div>
				@endforeach
			@endif
			@endisset
		</div>
		<div class="form-group">
			<a id="btn_add_option" href="javascript:;" class="font-pri-th fs-11" onclick="addOptionRow()"><i class="fas fa-plus"></i> เพิ่มตัวเลือก</a>
		</div>
	</div>
</div>


@push('additional_js')
<script type="text/javascript">
function removeOptionRow(t) 
{
	var opt_val = $.trim($(t).siblings(':text').val());
	if (opt_val != "") {
		if (confirm('Are you sure you want to delete '+ opt_val + '?')) {
			$(t).closest('.row-option').remove();
		}
	}
	else {
		$(t).closest('.row-option').remove();
	}
}
function addOptionRow()
{
	if (
			($('.row-option:visible').length > 0 && $.trim($(':text:visible:last').val()) !== "") || ($('.row-option:visible').length <= 0)
		) {
		$('.tpl-option').clone().removeClass('tpl-option d-none').addClass('d-flex').appendTo('#panel_options');
	}
	$(':text:visible:last').focus();
}

$(function() {
   addOptionRow();
})
</script>
@endpush