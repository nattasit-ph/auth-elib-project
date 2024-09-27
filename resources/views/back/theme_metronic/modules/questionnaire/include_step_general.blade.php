<form id="frm_main" class="px-4" method="POST">
	@csrf
	@method('POST')
	<input type="hidden" id="step" name="step" value="general">
	<input type="hidden" id="save_option" name="save_option" value="">
	<input type="hidden" id="id" name="id" value="{{ $content->id ?? '' }}">
	<input type="hidden" name="lang" value="{{ $lang ?? config('bookdose.frontend_default_lang') }}">

	<div class="kt-portlet__body">
		<div class="kt-section mb-0">
			<div class="form-group">
				<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>ชื่อฟอร์ม:</label>
				<input name="title" type="text" class="form-control required" placeholder="Enter title" value="{{ $content->title ?? old('title') }}" autocomplete="off">
			</div>

			<div class="form-group">
				<label>คำอธิบายเพิ่มเติมเกี่ยวกับฟอร์มนี้:</label>
				<div class="form-group">
					<textarea class="form-control summernote" name="description" rows="5">{{ $content->description ?? old('description') }}</textarea>
					<div class="my-3">
					<span class="form-text text-muted">เป็นข้อความที่ใช้แสดงด้านบนของแบบฟอร์ม</span>
				</div>
				</div>
			</div>

			<div class="form-group">
				<label>Pretty URL:</label>
				<input name="slug" type="text" class="form-control" placeholder="Enter pretty URL" value="{{ $content->slug ?? old('slug') ?? $slug_init }}" autocomplete="off">
				<div class="my-3">
					<span class="form-text text-muted">อนุญาตให้เฉพาะตัวอักษรภาษาอังกฤษ (a-z), ตัวเลขอารบิค (0-9), เครื่องหมาย - (Hyphen) และเครื่องหมาย _ (Underscore) เท่านั้น </span>
				</div>
			</div>
		
			<div class="form-group">
				<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>อีเมลผู้รับผิดชอบ:</label>
				<input name="contact_email" type="text" class="form-control required" placeholder="Enter email address" value="{{ $content->contact_email ?? old('contact_email') }}" autocomplete="off">
				<div class="my-3">
					<span class="form-text text-muted">อีเมลที่จะได้รับเมลแจ้งจากระบบ เมื่อมีผู้ใช้งานทำการตอบแบบฟอร์มเข้ามา</span>
				</div>
			</div>

			<div class="form-group">
				<label>สถานะ:</label>
				<div class="kt-radio-inline">
					<label class="kt-radio kt-radio--bold kt-radio--brand kt-radio--check-bold">
						 <input type="radio" name="status" value="1" checked=""> Active
						 <span></span>
					</label>
					<label class="kt-radio kt-radio--bold kt-radio--brand">
						 @if (isset($content))
						 	<input type="radio" name="status" value="0" {{ $content->status == '0' ? 'checked' : '' }}> Inactive
						 @else
						 	<input type="radio" name="status" value="0" checked=""> Inactive
						 @endif
						 <span></span>
					</label>
				 </div>
			</div>

		</div>
	</div>
</form>