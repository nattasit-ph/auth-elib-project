<!-- Modal-->
<div class="modal fade" id="modal_category" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modal_article_category_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-pri" id="modal_article_category_label">สร้างหมวดหมู่ใหม่</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
            	<div class="loading tmp" class="d-none">Loading...</div>
            	<div class="saving tmp" class="d-none">Saving...</div>
             	<div class="form-group">
                	<input type="text" id="category_title" name="category_title" class="form-control" placeholder="e.g. Sciences, Technology, Politics, etc.">
             	</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveCategory()">Save changes</button>
            </div>
        </div>
    </div>
</div>

@push ('additional_js')
<script type="text/javascript">
$('#modal_category').on('shown.bs.modal', function (event) {
	$(this).find('.tmp').addClass('d-none');
	$(this).find(':text:first').focus();
});
$('#modal_category').on('hidden.bs.modal', function (event) {
	$(this).find(':text:first').val('');
	$(this).find('.tmp').addClass('d-none');
	$(this).find('.form-group').removeClass('d-none');
});

function saveCategory() 
{
	$.ajax({
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		url: "{{ route('admin.news.category.quickSave') }}",
		type: 'POST',
		data: { 
			title: $('#category_title').val(),
		},
		dataType: 'json',
		beforeSend() {
			$(this).find('.form-group').addClass('d-none');
			$(this).find('.loading').addClass('d-none');
			$(this).find('.saving').removeClass('d-none');
		}
	})
	.done(function(resp) {
		$('#panel_field_categories').append(resp.html_categories);
		$('#modal_category').modal('hide');
	});
}
</script>
@endpush