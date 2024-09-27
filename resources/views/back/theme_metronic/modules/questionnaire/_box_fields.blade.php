

<div class="kt-portlet">
	<div class="kt-portlet__head bg-info">
		<div class="kt-portlet__head-label">
			<h3 id="section_title" class="kt-portlet__head-title font-pri text-white">
				<i class="far fa-align-left mr-2"></i> จัดการฟิลด์
			</h3>
		</div>
	</div>
	<div class="kt-portlet__body kt-portlet__body--fit">
		<div class="kt-portlet__body">
			@isset ($content->id)
			<div class="form-group">
				<div id="panel_fields">
					
				</div>
				
				<div class="tpl_panel_field box_field p-4 mb-4 d-none">
					<div class="row">
						<div class="form-group col-md-6">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ชื่อฟิลด์:</label>
							<input name="label" type="text" class="form-control required" placeholder="e.g. ชื่อ-สกุล/Full name" value="" autocomplete="off">
						</div>

						<div class="form-group col-md-6">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ประเภทฟิลด์:</label>
							<select class="custom-select form-control ddl_input_type" name="input_type">
								<option value="text">Textbox</option>
								<option value="textarea">Paragraph</option>
								<option value="dropdown">Dropdown list</option>
								<option value="checkbox">Checkboxes</option>
								<option value="radio">Radio buttons</option>
							</select>
						</div>
					</div>

					<div class="box_options form-group hide d-none">
						<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> ตัวเลือก <span></span>:</label>
						<div class="panel_options">
							<div class="tpl_option row mb-2">
								<div class="col-10">
									<input name="option[]" type="text" class="form-control" placeholder="ตัวเลือก" value="" autocomplete="off">
								</div>
								<div class="col-2 d-flex align-items-center">
									<a href="javascript:void(0);" class="text-danger font-sec-th btn_delete_option"><i class="fas fa-times mr-2"></i> ลบตัวเลือกนี้</a>
								</div>
							</div>
						</div>
						<div class="mt-3">
							<a href="javascript:void(0);" class="btn_add_option font-sec-th"><i class="fas fa-plus mr-2"></i> เพิ่มตัวเลือก</a>
						</div>
					</div>

					<div class="form-group hide">
						<label>ข้อความช่วยเหลือ:</label>
						<input name="help_text[]" type="text" class="form-control required" placeholder="ระบุข้อความช่วยเหลือเพื่อแสดงไว้ใต้ฟิลด์" value="" autocomplete="off">
						<div class="my-3">
							<span class="form-text text-muted">จะแสดงข้อความไว้ใต้ฟิลด์ในลักษณะนี้</span>
						</div>
					</div>

					<div class="form-group hide row mb-0">
						<div class="form-group col-md-6 mb-0">
							<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup> จำเป็นต้องระบุ (Required?):</label>
							<div class="kt-radio-inline">
								<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
									 <input type="radio" name="is_required" value="1" checked=""> Yes
									 <span></span>
								</label>
								<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
									 <input type="radio" name="is_required" value="0"> No
									 <span></span>
								</label>
							 </div>
						 </div>
						 <div class="form-group col-md-6 mb-0 d-flex align-items-end justify-content-end">
							<a href="javascript:void(0);" class="btn_delete_field text-danger font-sec-th" onclick="deleteFormField(this)"><i class="fas fa-trash mr-2"></i>ลบฟิลด์นี้</a>
						</div>
					</div>
						
				</div>


				<div class="mt-4">
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle font-pri" type="button" id="ddl_add_field" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="far fa-plus mr-2"></i>เพิ่มฟิลด์ใหม่
						</button>
						<div class="dropdown-menu" aria-labelledby="ddl_add_field">
						 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField(this)" data-val="text"><i class="fas fa-font mr-2"></i> Textbox</a>
						 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField(this)" data-val="textarea"><i class="fas fa-align-left mr-2"></i>Paragraph</a>
						 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField(this)" data-val="dropdown"><i class="far fa-caret-square-down mr-2"></i>Dropdown list</a>
						 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField(this)" data-val="checkbox"><i class="fas fa-check-square mr-2"></i> Checkboxes</a>
						 <a class="dropdown-item" href="javascript:void(0);" onclick="addFormField(this)" data-val="radio"><i class="fas fa-dot-circle mr-2"></i> Radio buttons</a>
						</div>
					</div>
				</div>
			</div>
			@else
				<p class="font-pri lead text-info">กรุณากดปุ่ม Save 1 ครั้งก่อนทำการเพิ่มฟิลด์</p>
			@endisset
		</div>

	</div>
</div>
