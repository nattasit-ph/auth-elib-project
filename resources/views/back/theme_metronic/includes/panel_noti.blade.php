<!-- begin::Quick Panel -->
<div id="kt_quick_panel" class="kt-quick-panel">
	<a href="#" class="kt-quick-panel__close" id="kt_quick_panel_close_btn"><i class="flaticon2-delete"></i></a>
	<div class="kt-quick-panel__nav">
		<div class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand  kt-notification-item-padding-x">
			<div class="d-flex justify-content-between w-100 mr-5">
				<h5>Notifications</h5>
				{{--<div><a href="#"><u>Mark all as read</u></a></div>--}}
			</div>
		</div>
		{{--
		<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand  kt-notification-item-padding-x" role="tablist">
			<li class="nav-item active">
				<a class="nav-link active" data-toggle="tab" href="#kt_quick_panel_tab_notifications" role="tab">Notifications</a>
			</li>
			<li class="nav-item">
				<a class="">Mark all as read</a>
			</li>
		</ul>
		--}}
	</div>


	<div class="kt-quick-panel__content">
		<div class="tab-content">
			<div class="tab-pane fade show kt-scroll active" id="kt_quick_panel_tab_notifications" role="tabpanel">
				<div class="kt-notification">
					<div id="noti_list"></div>	
				</div>
			</div>
		</div>
	</div>
</div>

<!-- end::Quick Panel -->