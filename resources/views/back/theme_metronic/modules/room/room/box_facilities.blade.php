<div id="dynamic_row">
	@if(!empty($content->facilities))
		<?php 
			$row_facilities = 0;
		?>
		@foreach ($content->facilities as $value)
		<?php $row_facilities++;?>
		<div class="row" id="row_fac_{{$row_facilities}}">
			<div class="form-group col-8">
				<input name="facilities[]" type="text" class="form-control" placeholder="e.g. Projector, 55-inch TV" value="{{$value}}" autocomplete="off">
			</div>
			<div class="form-group col-4">
				<button type="button" name="row_fac_{{$row_facilities}}" id="remove_fac_{{$row_facilities}}" class="btn btn-danger btn_remove"><i class="far fa-times pr-0"></i></button>
			</div>
		</div>
		@endforeach
		<input type="hidden" id="num_fac" name="num_fac" value="{{$row_facilities}}">	
	@else
		<div class="row">
			<div class="form-group col-8">
				<input name="facilities[]" type="text" class="form-control" placeholder="e.g. Projector, 55-inch TV" value="" autocomplete="off">
			</div>
		</div>
	@endif

</div>

<div class="row">
	<div class="form-group col">
		<a class="font-sec-th" id="add" href="javascript:void(0);"><i class="fas fa-plus mr-2"></i> เพิ่มรายการสิ่งอำนวยความสะดวก</a>
	</div>
</div>
