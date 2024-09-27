@if(isset($allTagAndCategory['article']))
<div class="form-group">
    <label>หมวดหมู่ Article:</label><br>
    <div class="row">
        @forelse ($allTagAndCategory['article'] as $c)
        <div class="col-3">
			<div class="kt-checkbox-inline">
                @if(isset($interest))
                <label class="kt-checkbox kt-checkbox--brand kt-checkbox--{{ (in_array($c->slug, (json_decode($interest->data_article) ?? []))) ? 'solid' : 'bold' }}">
					 <input type="checkbox" name="data_article[]" value="{{ $c->slug }}" {{ (in_array($c->slug, (json_decode($interest->data_article) ?? []))) ? 'checked' : '' }}> {{ $c->title }}
					 <span></span>
				</label>
                @else
				<label class="kt-checkbox kt-checkbox--brand kt-checkbox--bold">
					 <input type="checkbox" name="data_article[]" value="{{ $c->slug }}"> {{ $c->title }}
					 <span></span>
				</label>
                @endif
			 </div>
		</div>
        @empty
        @endforelse
    </div>
</div>
@endif

@if(isset($allTagAndCategory['podcast']))
<div class="form-group">
    <label>หมวดหมู่ Podcast:</label><br>
    <div class="row">
        @forelse ($allTagAndCategory['podcast'] as $c)
        <div class="col-3">
			<div class="kt-checkbox-inline">
                @if(isset($interest))
                <label class="kt-checkbox kt-checkbox--brand kt-checkbox--{{ (in_array($c->slug, (json_decode($interest->data_podcast) ?? []))) ? 'solid' : 'bold' }}">
					 <input type="checkbox" name="data_podcast[]" value="{{ $c->slug }}" {{ (in_array($c->slug, (json_decode($interest->data_podcast) ?? []))) ? 'checked' : '' }}> {{ $c->title }}
					 <span></span>
				</label>
                @else
				<label class="kt-checkbox kt-checkbox--brand kt-checkbox--bold">
					 <input type="checkbox" name="data_podcast[]" value="{{ $c->slug }}"> {{ $c->title }}
					 <span></span>
				</label>
                @endif
			 </div>
		</div>
        @empty
        @endforelse
    </div>
</div>
@endif

@if(isset($allTagAndCategory['library']))
<div class="form-group">
    <label>หมวดหมู่ Library:</label><br>
    <select class="form-control" id="ddl_categories_library" name="data_library[]" multiple="true" style="line-height: 2em;">
        <option value="">ทั่วไป</optÎion>
            @forelse ($allTagAndCategory['library']->toArray() as $c)
        <option>{{ Arr::get($c, 'name.th') }}</option>
        @empty
        @endforelse
    </select>
    <div class="my-2">
        <span class="form-text text-muted">เคาะ Enter เพื่อแยกคำ</span>
    </div>
</div>
@endif

@if(isset($allTagAndCategory['elibrary']))
<div class="form-group">
    <label>หมวดหมู่ eLibrary:</label><br>
    <select class="form-control" id="ddl_categories_elibrary" name="data_elibrary[]" multiple="true" style="line-height: 2em;">
        <option value="">ทั่วไป</optÎion>
            @forelse ($allTagAndCategory['elibrary']->toArray() as $c)
        <option>{{ Arr::get($c, 'name.th') }}</option>
        @empty
        @endforelse
    </select>
    <div class="my-2">
        <span class="form-text text-muted">เคาะ Enter เพื่อแยกคำ</span>
    </div>
</div>
@endif

@if(isset($allTagAndCategory['tag']))
<div class="form-group">
    <label>Tags:</label><br>
    <select class="form-control col-12" id="ddl_tags" name="data_tags[]" multiple="true" style="line-height: 2em;">
        @forelse ($allTagAndCategory['tag']->toArray() as $c)
        <option>{{ Arr::get($c, 'name.th') }}</option>
        @empty
        @endforelse
    </select>
    <div class="my-2">
        <span class="form-text text-muted">
            เคาะ Enter เพื่อแยกคำ
        </span>
    </div>
</div>
@endif

@push('additional_js')
<script type="text/javascript">
$(document).ready(function() {
    $('#ddl_categories_elibrary').select2({
			placeholder: "Choose elibrary categories",
			tags: true,
			tokenSeparators: [","],
		});

		$('#ddl_categories_library').select2({
			placeholder: "Choose library categories",
			tags: true,
			tokenSeparators: [","],
		});

		$('#ddl_tags').select2({
			placeholder: "Choose tags",
			tags: true,
			tokenSeparators: [","],
		});
    @isset($interest)
            var arr_categories_library = [];
		    var arr_categories_elibrary = [];
		    var arr_tag = [];
			@if(json_decode($interest->data_library))
				@foreach(json_decode($interest->data_library) as $tag)
				var temp = "{{$tag}}";
                console.log(temp)
				arr_categories_library.push(temp.replace('&amp;', '&'));
				@endforeach
				$("#ddl_categories_library").val(arr_categories_library).trigger('change');
			@endif
			@if(json_decode($interest->data_elibrary))
				@foreach(json_decode($interest->data_elibrary) as $tag)
				var temp = "{{$tag}}";
				arr_categories_elibrary.push(temp.replace('&amp;', '&'));
				@endforeach
				$("#ddl_categories_elibrary").val(arr_categories_elibrary).trigger('change');
			@endif
			@if(json_decode($interest->data_tags))
				@foreach(json_decode($interest->data_tags) as $tag)
				var temp = "{{$tag}}";
				arr_tag.push(temp.replace('&amp;', '&'));
				@endforeach
				$("#ddl_tags").val(arr_tag).trigger('change');
			@endif
		@endisset
	});
</script>
@endpush