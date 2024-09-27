<div id="panel_field_categories" class="row">
	@forelse ($categories as $c)
		<div class="col-3">
			<div class="kt-checkbox-inline">
				<label class="kt-checkbox kt-checkbox--brand kt-checkbox--{{ ($c->id == ($selected_id ?? '') || in_array($c->id, ($article->selected_categories ?? []))) ? 'solid' : 'bold' }}">
					 <input type="checkbox" name="article_categories[]" value="{{ $c->id }}" {{ ($c->id == ($selected_id ?? '') || in_array($c->id, ($article->selected_categories ?? []))) ? 'checked' : '' }}> {{ $c->title }}
					 <span></span>
				</label>
			 </div>
		</div>
	@empty
		<div class="col font-pri">ยังไม่มีการสร้างหมวดหมู่ไว้ กรุณา 
			<a href="javascript:void(0);" onclick="addNewCategory()">
				<strong>คลิกที่นี่เพื่อเพิ่มหมวดหมู่ใหม่</strong>
			</a>
		</div>
	@endforelse
</div>

@if (isset($categories) && $categories->count() > 0)
	<div class="row mt-4">
		<div class="col font-pri">
			<a href="javascript:void(0);" onclick="addNewCategory()">
				<i class="far fa-plus mr-2"></i>เพิ่มหมวดหมู่ใหม่
			</a>
			</div>
	</div>
@endif