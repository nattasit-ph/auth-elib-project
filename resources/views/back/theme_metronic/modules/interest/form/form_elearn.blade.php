@if(isset($allTagAndCategory['elearning']))
<div class="form-group">
    <label>หมวดหมู่ elearning:</label><br>
    <div class="row">
        @forelse ($allTagAndCategory['elearning'] as $c)
		@if($c['status'])
        <div class="col-3">
			<div class="kt-checkbox-inline">
                @if(isset($interest))
                <label class="kt-checkbox kt-checkbox--brand kt-checkbox--{{ (in_array($c['slug'], (json_decode($interest->data_elearning) ?? []))) ? 'solid' : 'bold' }}">
					 <input type="checkbox" name="data_elearning[]" value="{{ $c['slug'] }}" {{ (in_array($c['slug'] , (json_decode($interest->data_elearning) ?? []))) ? 'checked' : '' }}> {{ $c['name'] }}
					 <span></span>
				</label>
                @else
				<label class="kt-checkbox kt-checkbox--brand kt-checkbox--bold">
					 <input type="checkbox" name="data_elearning[]" value="{{ $c['slug'] }}"> {{ $c['name'] }}
					 <span></span>
				</label>
                @endif
			 </div>
		</div>
		@endif
        @empty
        @endforelse
    </div>
</div>
@endif