<div class="row">

	<div class="form-group col-12 col-md-6">
		<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>ชื่อห้อง/สถานที่:</label>
		<input name="title" type="text" class="form-control required" placeholder="Enter title" value="{{ $content->title ?? old('title') }}" autocomplete="off">
	</div>

	<div class="form-group col-12 col-md-6">
		<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>ประเภทห้อง:</label>
		<select id="room_type_id" name="room_type_id" class="form-control required kt_datepicker" required>
			@if(!empty($content->room_type))
				<option value="{{$content->room_type->id}}">{{$content->room_type->title}}</option>
			@else
				<option value="">โปรดเลือก</option>
			@endif
		
			@forelse ($room_type as $item)
				<option value="{{ $item->id }}">{{ $item->title }}</option>
			@empty
				Record not found.
			@endforelse
		</select>
	</div>

	<div class="form-group col-12">
		<label><sup><i class="la la-asterisk fs-10 text-danger"></i>รายละเอียดเพิ่มเติมเกี่ยวกับห้อง/สถานที่:</label>
		<textarea id="description" name="description" class="form-control" placeholder="Enter description">{{ $content->description ?? old('description') }}</textarea>
	</div>

	<div class="form-group col-12 col-md-4">
		<label><sup><i class="la la-asterisk fs-10 text-danger"></i></sup>จำนวนที่นั่งที่รับได้สูงสุด:</label>
		<input name="max_seats" type="number" class="form-control" placeholder="e.g. 15-20" value="{{ $content->max_seats ?? old('max_seats') }}" autocomplete="off">
	</div>
	<div class="form-group col-12 col-md-4">
		<label>เวลาเปิด:</label>
		<select id="open_time" name="open_time" class="form-control">
			<option>{{ !empty($content->open_time) ? $content->open_time : '' }}</option>
			@if(!empty($content->open_time))
			<option>ไม่ระบุ</option>
			@endif
			@for($h=0; $h<=23; $h++)
			@for($m=0; $m<60; $m=$m+30)
				<option>{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
			@endfor
			@endfor
		</select>
		
		<div class="my-3">
			<span class="form-text text-muted">หากเปิด 24 ชม. โปรดเลือก "ไม่ระบุ"</span>
		</div>
	
	</div>
	<div class="form-group col-12 col-md-4">
		<label>เวลาปิด:</label>
		<select id="closed_time" name="closed_time" class="form-control">
			<option>{{ !empty($content->closed_time) ? $content->closed_time : '' }}</option>
			@if(!empty($content->closed_time))
			<option>ไม่ระบุ</option>
			@endif
			@for($h=0; $h<=23; $h++)
			@for($m=0; $m<60; $m=$m+30)
				<option>{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
			@endfor
			@endfor
		</select>
		
		<div class="my-3">
			<span class="form-text text-muted">หากเปิด 24 ชม. โปรดเลือก "ไม่ระบุ"</span>
		</div>

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


@push('additional_js')
<script type="text/javascript">
$(function() {
	$('#description').summernote({
		height: 150,
		callbacks:{
		onPaste: function(e) {
             console.log('paste it')
             const bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text')
             e.preventDefault()
             setTimeout(function () {
                 document.execCommand('insertText', false, bufferText)
             }, 10)
         },
		},
	});
})
</script>
@endpush
