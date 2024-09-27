@push('additional_css')
<style type="text/css">
.box_field { background: #f3f6f9 !important; }
.box_field.active { background: #E7F3FF !important; }
.box_heading { background: #e4e6ef !important; }
</style>
@endpush


<div class="kt-portlet__body px-5">
	@isset ($content->id)
	<form id="frm_main" name="frm_main" class="" method="POST">
		@csrf
		@method('POST')
		<input type="hidden" id="step" name="step" value="fields">
		<input type="hidden" id="form_id" name="form_id" value="{{ $content->id ?? '' }}">
		<input type="hidden" id="running" name="running" value="0">
		<div class="form-group">
			<div id="panel_fields">
				@if (!isset($fields) || $fields->count() <= 0)
					<p class="font-pri lead text-info">ยังไม่มีฟิลด์ในฟอร์มนี้ โปรดทำการเพิ่มฟิลด์</p>
				@endif
			</div>
		</div>
	</form>
			
	<div class="tpl_panel_section_label box_field box_heading p-4 mb-4 d-none">
		<input type="hidden" name="row_id[]" class="row_id" value="">
		<input type="hidden" name="id[]" class="" value="">
		<div class="form-group">
			<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> หัวข้อ Heading:</label>
			<input name="section_label[]" type="text" class="form-control form-control-lg txt_section_label border-0 required" placeholder="e.g. เสนอความคิดเห็นเพิ่มเติม" value="" autocomplete="off">
		</div>
		 <div class="form-group hi mb-0 text-right">
		 	<a href="javascript:void(0);" class="text-info font-sec mr-3" onclick="moveUp(this)"><i class="fas fa-chevron-up mr-2"></i>เลื่อนขึ้น</a>
			 	<a href="javascript:void(0);" class="text-info font-sec mr-3" onclick="moveDown(this)"><i class="fas fa-chevron-down mr-2"></i>เลื่อนลง</a>
			 	<a href="javascript:void(0);" class="text-info font-sec mr-3" onclick="replicateSectionLabel(this)"><i class="fas fa-copy mr-2"></i>ทำซ้ำฟิลด์นี้</a>
			<a href="javascript:void(0);" class="btn_delete_field text-danger font-sec" onclick="deleteFormField(this)"><i class="fas fa-trash mr-2"></i>ลบฟิลด์นี้</a>
		</div>
	</div>

	<div class="tpl_panel_field box_field p-4 mb-4 d-none">
		<div class="row">
			<input type="hidden" name="row_id[]" class="row_id" value="">
			<input type="hidden" name="id[]" class="" value="">
			<div class="form-group col-md-6">
				<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อฟิลด์:</label>
				<input name="label[]" type="text" class="form-control txt_label_name required" placeholder="e.g. ชื่อ-สกุล/Full name" value="" autocomplete="off">
			</div>

			<div class="form-group col-md-6">
				<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ประเภทฟิลด์:</label>
				<select class="custom-select form-control ddl_input_type" name="input_type[]">
					<option value="text">Textbox</option>
					<option value="textarea">Paragraph</option>
					<option value="dropdown">Dropdown list</option>
					<option value="checkbox">Checkboxes</option>
					<option value="radio">Radio buttons</option>
				</select>
			</div>
		</div>

		<div class="box_options form-group hi d-none">
			<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ตัวเลือก <span></span>:</label>
			<div class="panel_options">
				<div class="tpl_option row mb-2">
					<div class="col-10">
						<input name="option[]" type="text" class="tpl_txt_option form-control" placeholder="ตัวเลือก" value="" autocomplete="off">
					</div>
					<div class="col-2 d-flex align-items-center">
						<a href="javascript:void(0);" class="text-danger font-sec btn_delete_option"><i class="fas fa-times mr-2"></i> ลบตัวเลือกนี้</a>
					</div>
				</div>
			</div>
			<div class="mt-3">
				<a href="javascript:void(0);" class="btn_add_option font-sec"><i class="fas fa-plus mr-2"></i> เพิ่มตัวเลือก</a>
			</div>
		</div>

		<div class="form-group hi">
			<label>ข้อความช่วยเหลือ:</label>
			<input name="help_text[]" type="text" class="form-control txt_help_text" placeholder="ระบุข้อความช่วยเหลือเพื่อแสดงไว้ใต้ฟิลด์" value="" autocomplete="off">
			<div class="my-3">
				<span class="form-text text-muted">จะแสดงข้อความไว้ใต้ฟิลด์ในลักษณะนี้</span>
			</div>
		</div>

		<div class="form-group hi row mb-0">
			<div class="form-group col-md-6 mb-0">
				<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> จำเป็นต้องระบุ (Required?):</label>
				<div class="kt-radio-inline">
					<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
						 <input type="radio" name="is_required[]" value="1" checked=""> Yes
						 <span></span>
					</label>
					<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
						 <input type="radio" name="is_required[]" value="0"> No
						 <span></span>
					</label>
				 </div>
			 </div>
			 <div class="form-group col-md-6 mb-0 d-flex align-items-end justify-content-end">
			 	<a href="javascript:void(0);" class="text-info font-sec mr-3" onclick="moveUp(this)"><i class="fas fa-chevron-up mr-2"></i>เลื่อนขึ้น</a>
			 	<a href="javascript:void(0);" class="text-info font-sec mr-3" onclick="moveDown(this)"><i class="fas fa-chevron-down mr-2"></i>เลื่อนลง</a>
			 	<a href="javascript:void(0);" class="text-info font-sec mr-3" onclick="replicateField(this)"><i class="fas fa-copy mr-2"></i>ทำซ้ำฟิลด์นี้</a>
				<a href="javascript:void(0);" class="btn_delete_field text-danger font-sec" onclick="deleteFormField(this)"><i class="fas fa-trash mr-2"></i>ลบฟิลด์นี้</a>
			</div>
		</div>
	</div>

	<div class="mt-4">
		<div class="dropdown">
			<button class="btn btn-primary dropdown-toggle font-pri" type="button" id="ddl_add_field" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="far fa-plus mr-2"></i>เพิ่มแถวใหม่
			</button>
			<div class="dropdown-menu" aria-labelledby="ddl_add_field">
			 <a class="dropdown-item" href="javascript:void(0);" onclick="addSectionLabel()"><i class="fas fa-heading mr-2"></i> Heading</a>
			 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField('radio')"><i class="fas fa-dot-circle mr-2"></i> Radio buttons</a>
			 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField('checkbox')"><i class="fas fa-check-square mr-2"></i> Checkboxes</a>
			 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField('dropdown')"><i class="far fa-caret-square-down mr-2"></i>Dropdown list</a>
			 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField('text')"><i class="fas fa-text-size mr-2"></i> Textbox</a>
			 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField('textarea')"><i class="fas fa-align-left mr-2"></i>Paragraph</a>
			</div>
		</div>
	</div>
	@else
		<p class="font-pri lead text-info">กรุณากดปุ่ม Save  1 ครั้งก่อนทำการเพิ่มฟิลด์</p>
	@endisset
</div>


@push('additional_js')
<script type="text/javascript">
function moveUp(t)
{
	event.preventDefault();
	event.stopPropagation();
	var e = $(t).closest('.box_field');
	$(e).insertBefore($(e).prev());
}

function moveDown(t)
{
	event.preventDefault();
	event.stopPropagation();
	var e = $(t).closest('.box_field');
	$(e).insertAfter($(e).next());
}

function replicateSectionLabel(t)
{
	event.preventDefault();
	event.stopPropagation();
	var e = $(t).closest('.box_field');

	var row_id = $('#running').val();
	var new_row_id = parseInt( parseInt(row_id)+1);
	$('#running').val(new_row_id);

	$('.box_field').removeClass('active');
	$('.form-group.hi').addClass('d-none');
	
	var new_e = $(e).clone();
	$(new_e).find('.row_id').val(row_id);
	$(e).removeClass('active');
	$(new_e).addClass('active').removeClass('tpl_panel_section_label').find(':text:first').focus();
	$(new_e).find('.form-group.hi').removeClass('d-none');
	// Rename name attr
	$(new_e).find('input[name="id[]"]').val('');
	$(new_e).find('input.txt_section_label').attr('name', 'section_label_'+row_id);

	$(new_e).insertAfter($(e));
	$(new_e).find(':text:first').focus().trigger('click');
}

function addSectionLabel(id, section_label)
{
	var row_id = $('#running').val();
	$('.tpl_panel_section_label').clone().removeClass('d-none').appendTo('#panel_fields');
	$('.box_field:visible:last').find('.row_id').val(row_id);
	
	var new_row_id = parseInt( parseInt(row_id)+1);
	$('#running').val(new_row_id);

	$('.box_field').removeClass('active');
	$('.form-group.hi').addClass('d-none');

	var b = $('.box_field:visible:last');
	$(b).addClass('active').removeClass('tpl_panel_section_label').find(':text:first').focus();
	$(b).find('.form-group.hi').removeClass('d-none');
	
	// Init (if any)
	if (id) { $(b).find('input[name="id[]"]').val(id); }
	if (section_label) { $(b).find('input[name="section_label[]"]').val(section_label); }

	// Rename name attr
	$(b).find('input[name="section_label[]"]').attr('name', 'section_label_'+row_id);
}

function replicateField(t)
{
	event.preventDefault();
	event.stopPropagation();
	var e = $(t).closest('.box_field');
	var input_type = $(e).find('.ddl_input_type').val();

	var row_id = $('#running').val();
	var new_row_id = parseInt( parseInt(row_id)+1);
	$('#running').val(new_row_id);

	$('.box_field').removeClass('active');
	$('.form-group.hi').addClass('d-none');
	
	var new_e = $(e).clone();
	$(new_e).find('.row_id').val(row_id);
	$(e).removeClass('active');
	$(new_e).addClass('active').removeClass('tpl_panel_field').find(':text:first').focus();
	$(new_e).find('.form-group.hi').removeClass('d-none');
	$(new_e).find('.ddl_input_type').val(input_type).trigger('change');
	// Rename name attr
	$(new_e).find('input[name="id[]"]').val('');
	$(new_e).find('input.txt_label_name').attr('name', 'label_'+row_id);
	$(new_e).find('select.ddl_input_type').attr('name', 'input_type_'+row_id);
	$(new_e).find('input.txt_help_text').attr('name', 'help_text_'+row_id);
	$(new_e).find('input.tpl_txt_option').attr('name', 'option_'+row_id+'[]');
	$(new_e).find(':radio').attr('name', 'is_required_'+row_id);

	$(new_e).insertAfter($(e));
	$(new_e).find(':text:first').focus().trigger('click');
}

function addFormField(input_type, id, label, help_text, options, is_required)
{
	var row_id = $('#running').val();
	$('.tpl_panel_field').clone().removeClass('d-none').appendTo('#panel_fields');
	$('.box_field:visible:last').find('.row_id').val(row_id);
	
	var new_row_id = parseInt( parseInt(row_id)+1);
	$('#running').val(new_row_id);

	$('.box_field').removeClass('active');
	$('.form-group.hi').addClass('d-none');

	var b = $('.box_field:visible:last');
	$(b).addClass('active').removeClass('tpl_panel_field').find(':text:first').focus();
	$(b).find('.form-group.hi').removeClass('d-none');
	
	// Init (if any)
	$(b).find('.ddl_input_type').val(input_type).trigger('change');
	if (id) { $(b).find('input[name="id[]"]').val(id); }
	if (label) { $(b).find('input[name="label[]"]').val(label); }
	if (help_text) { $(b).find('input[name="help_text[]"]').val(help_text); }
	if (is_required) { $(b).find('input:radio').filter('[value='+is_required+']').prop('checked', true); }
	if (options !== '' && options !== null && options !== undefined) { 
		var obj = JSON.parse(options);
		for(var k in obj) {
			if (k > 0) $(b).find('.btn_add_option').trigger('click'); 
			$(b).find('input.tpl_txt_option:last').val(obj[k]);
		}
	}

	// Rename name attr
	$(b).find('input[name="label[]"]').attr('name', 'label_'+row_id);
	$(b).find('select[name="input_type[]"]').attr('name', 'input_type_'+row_id);
	$(b).find('input[name="help_text[]"]').attr('name', 'help_text_'+row_id);
	$(b).find('input.tpl_txt_option').attr('name', 'option_'+row_id+'[]');
	$(b).find(':radio').attr('name', 'is_required_'+row_id);
}
function deleteFormField(t) 
{
	event.preventDefault();
	event.stopPropagation();
	if (confirm('Are you sure you want to delete this field?')) {
		$(t).closest('.box_field').remove();
	}
}

$(function() {
	$('body').delegate('.ddl_input_type', 'change', function(e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).closest('.box_field').find('.box_options').addClass('d-none')
		if ($(this).val() == 'dropdown' || $(this).val() == 'checkbox' || $(this).val() == 'radio') {
			$(this).closest('.box_field').find('.box_options').removeClass('d-none');
			// $(this).closest('.box_field').find('.btn_add_option').trigger('click');
			// $(this).closest('.box_field').find('.box_options').find(':text:first').focus();
		}
	});

	$('body').delegate('.btn_add_option', 'click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		var b = $(this).closest('div').siblings('.panel_options');
		$(b).find('.tpl_option:first').clone().appendTo($(b));
		$(b).find('input.tpl_txt_option:last').removeClass('d-none');
		$(b).find(':text:last').val("").focus();
	});

	$('body').delegate('.btn_delete_option', 'click', function() {
		if (confirm('Are you sure you want to delete this option?')) {
			$(this).closest('.tpl_option').remove();
		}
	});

	$('body').delegate('.btn_delete_field', 'click', function() {
		if (confirm('Are you sure you want to delete this field?')) {
			$(this).closest('.box_field').remove();
		}
	});

	$('body').delegate('input, select', 'focus', function(e) {
		e.preventDefault();
		$('.box_field').removeClass('active');
		$(this).closest('.box_field').addClass('active');
		$('.form-group.hi').addClass('d-none');
		$(this).closest('.box_field').find('.form-group.hi').removeClass('d-none');
		$(this).closest('.box_field').find('.ddl_input_type').trigger('change');
	});


	// Init for edit mode
	@if (isset($fields) && $fields->count() > 0)
		@foreach ($fields as $f)
			@if (!empty($f->section_label))
				addSectionLabel('{{ $f->id }}', '{{ $f->section_label }}');
			@else
				addFormField('{{ $f->input_type }}', '{{ $f->id }}', '{{ $f->label }}', '{{ $f->help_text }}', '{{ json_encode($f->options) }}'.replace(/&quot;/g, '"'), '{{ $f->is_required }}');
			@endif
		@endforeach
	@endif
});
</script>
@endpush