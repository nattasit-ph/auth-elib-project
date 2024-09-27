<!-- begin::Global Config(global config for global JS sciprts) -->
<script>
	WebFont.load({
		google: {
			"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
		},
		active: function() {
			sessionStorage.fonts = true;
		}
	});

	var KTAppOptions = {
		"colors": {
			"state": {
				"brand": "#5d78ff",
				"dark": "#282a3c",
				"light": "#ffffff",
				"primary": "#5867dd",
				"success": "#34bfa3",
				"info": "#36a3f7",
				"warning": "#ffb822",
				"danger": "#fd3995"
			},
			"base": {
				"label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
				"shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
			}
		}
	};



	var delayLoading = 200;

	function save(opt=1) {
		$('#btn_save').prop('disabled', 'disabled').text('Saving...');
		$('#save_option').val(opt);
		$('#frm_main').submit();
	}

	function saveAndContinue() {
		$('#btn_save').prop('disabled', 'disabled').text('Saving...');
		$('#save_option').val('2');
		$('#frm_main').submit();
	}

	function showNotifyOnScreen(resp)
	{
		var content = {};
	   content.title = resp.notify_title;
	   content.message = resp.notify_msg;
	   content.icon = resp.notify_icon;
	   
	   var notify = $.notify(content, { 
	    	type: resp.notify_type,
			allow_dismiss: true,
			newest_on_top: false,
			showProgressbar: false,
			placement: {
				from: "top",
				align: "right"
			},
	       spacing: 10,
	       timer: 2000,
	       offset: {
	           x: 30, 
	           y: 20
	       },
	       delay: 1000,
	       z_index: 10000,
	       animate: {
				enter: 'animated fadeInDown',
				exit: 'animated fadeOutUp'
			 },
	   });
	}

	function sendNotification(e) 
	{
		$('#modal_noti_item_type').val($(e).data('type'));
		$('#modal_noti_item_id').val($(e).data('id'));
		$('#modal_noti').modal('show');
	}

	function duplicateItem(e) 
	{
		var id = $(e).data('id');
		var url = $('#page_url_duplicate').val();

		var confirm_line_1 = 'Are you sure you want to duplicate \n'+$(e).data('title')+'?';
		var confirm_line_2 = '';
		
		const swalWithBootstrapButtons = Swal.mixin({
	  	customClass: {
		    confirmButton: 'btn btn-brand',
		    cancelButton: 'btn btn-default'
		  },
		  buttonsStyling: false
		})

		swalWithBootstrapButtons.fire({
		  title: confirm_line_1,
		  text: confirm_line_2,
		  type: 'warning',
		  showCancelButton: !0,
		  confirmButtonText: 'Yes, duplicate please!',
		  // cancelButtonText: 'Cancel!',
		}).then((result) => {
			if (result.value) {
				$.ajaxSetup({
				    headers: {
				        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    }
				});
				$.ajax({
					url: url,
					method: 'POST',
					data: { id: id, name: "{{ $name ?? '' }}", lang: "{{ $lang ?? app()->getLocale() }}" },
					dataType: 'json',
				}).done(function(response) {
					if (response.status == '200') {
						if (response.redirect_url != '')
							window.location = response.redirect_url;
						else
							initDataTable();
					}
					showNotifyOnScreen(response);
				});
			}
		})
	}

	function deleteItem(e) 
	{
		var id = $(e).data('id');
		var id2 = $(e).data('id2');
		var url = $('#page_url_delete').val();
		var isleaf = $(e).attr('data-isleaf');

		var confirm_line_1 = 'Are you sure you want to delete \n'+$(e).data('title')+'?';
		var confirm_line_2 = '';
		
		if (typeof isleaf !== typeof undefined && isleaf !== false) {
			if (isleaf != '1') confirm_line_2 = 'WARNING! This menu and all sub menus will be deleted!';
		}

		const swalWithBootstrapButtons = Swal.mixin({
	  	customClass: {
		    confirmButton: 'btn btn-brand',
		    cancelButton: 'btn btn-default'
		  },
		  buttonsStyling: false
		})

		swalWithBootstrapButtons.fire({
		  title: confirm_line_1,
		  text: confirm_line_2,
		  type: 'warning',
		  showCancelButton: !0,
		  confirmButtonText: 'Yes, delete it!',
		  // cancelButtonText: 'Cancel!',
		}).then((result) => {
			if (result.value) {
				$.ajaxSetup({
				    headers: {
				        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    }
				});
				$.ajax({
					url: url,
					method: 'POST',
					data: { id: id, id2: id2, name: "{{ $name ?? '' }}", lang: "{{ $lang ?? app()->getLocale() }}" },
					dataType: 'json',
				}).done(function(response) {
					if (response.status == '200') {
						initDataTable();
					}
					showNotifyOnScreen(response);
						// swalWithBootstrapButtons.fire(
					 //      response.notify_title,
					 //      response.notify_msg,
					 //      response.notify_type,
				  //   	)
					
				});
			}
		})

	}

	function logout() {
		document.getElementById('frm_logout').submit();
	}
	
	function toggleStatus(e) 
	{
		var id = $(e).data('id');
		var status = $(e).data('status');
		var url = $('#page_url_set_status').val();

		const swalWithBootstrapButtons = Swal.mixin({
	  	customClass: {
		    confirmButton: 'btn btn-brand',
		    cancelButton: 'btn btn-default'
		  },
		  buttonsStyling: false
		})

		swalWithBootstrapButtons.fire({
		  title: 'Are you sure you want to '+ (status == '1' ? 'inactivate ' : 'activate ') + $(e).data('title')+'?',
		  // text: confirm_line_2,
		  type: 'warning',
		  showCancelButton: !0,
		  confirmButtonText: 'Yes, '+(status == '1' ? 'inactivate ' : 'activate ')+' it!',
		  // cancelButtonText: 'Cancel!',
		}).then((result) => {
			if (result.value) {
				$.ajaxSetup({
				    headers: {
				        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    }
				});
				$.ajax({
					url: url,
					method: 'POST',
					data: { id: id, status: status, name: "{{ $name ?? '' }}", lang: "{{ $lang ?? app()->getLocale() }}" },
					dataType: 'json',
				}).done(function(response) {
					if (response.status == '200') {
						initDataTable();
					}
					showNotifyOnScreen(response);
						// swalWithBootstrapButtons.fire(
					 //      response.notify_title,
					 //      response.notify_msg,
					 //      response.notify_type,
				  //   	)
					
				});
			}
		})
	}

	function toggleHighlight(e) 
	{
		var id = $(e).data('id');
		var status = $(e).data('status');
		var url = $('#page_url_set_highlight').val();

		const swalWithBootstrapButtons = Swal.mixin({
	  	customClass: {
		    confirmButton: 'btn btn-brand',
		    cancelButton: 'btn btn-default'
		  },
		  buttonsStyling: false
		})

		swalWithBootstrapButtons.fire({
		  title: 'Are you sure you want to '+ (status == '1' ? 'Remove content from Highlight ' : 'Set content as Highlight ')+'?',
		  // text: confirm_line_2,
		  type: 'warning',
		  showCancelButton: !0,
		  confirmButtonText: 'Yes, '+(status == '1' ? 'remove from highlight ' : 'set as highlight ')+'!',
		  // cancelButtonText: 'Cancel!',
		}).then((result) => {
			if (result.value) {
				$.ajaxSetup({
				    headers: {
				        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    }
				});
				$.ajax({
					url: url,
					method: 'POST',
					data: { id: id, status: status, name: "{{ $name ?? '' }}", lang: "{{ $lang ?? app()->getLocale() }}" },
					dataType: 'json',
				}).done(function(response) {
					if (response.status == '200') {
						initDataTable();
					}
					showNotifyOnScreen(response);
						// swalWithBootstrapButtons.fire(
					 //      response.notify_title,
					 //      response.notify_msg,
					 //      response.notify_type,
				  //   	)
					
				});
			}
		})
	}

	function moveUp(e) 
	{
		var id = $(e).data('id');
		var status = $(e).data('status');
		var url = $('#page_url_move').val();
		// if (confirm('Are you sure you want to move '+$(e).data('title')+' up?')) {
			$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    }
			});
			$.ajax({
				url: url,
				method: 'POST',
				data: { id: id, direction: 'up', name: "{{ $name ?? '' }}", lang: "{{ $lang ?? app()->getLocale() }}" },
				dataType: 'json',
			}).done(function(response) {
				if (response.status == '200') {
					initDataTable();
				}
				showNotifyOnScreen(response);
			});
		// }
		// else {
		// 	return true;
		// }
	}

	function moveDown(e) 
	{
		var id = $(e).data('id');
		var status = $(e).data('status');
		var url = $('#page_url_move').val();

		// if (confirm('Are you sure you want to move '+$(e).data('title')+' down?')) {
			$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    }
			});
			$.ajax({
				url: url,
				method: 'POST',
				data: { id: id, direction: 'down', name: "{{ $name ?? '' }}", lang: "{{ $lang ?? app()->getLocale() }}" },
				dataType: 'json',
			}).done(function(response) {
				if (response.status == '200') {
					initDataTable();
				}
				showNotifyOnScreen(response);
			});
		// }
		// else {
		// 	return true;
		// }
	}


	function styleCheckbox(t) {
		if ($(t).is(':checked')) {
			$(t).closest('label').removeClass('kt-checkbox--bold').addClass('kt-checkbox--solid');
		}
		else {
			$(t).closest('label').removeClass('kt-checkbox--solid').addClass('kt-checkbox--bold');
		}
	}



	$(function() {
			
		$(':checkbox').change(function(e) {
			e.preventDefault();
			styleCheckbox($(this));
		})

		$('#btn_clear_manage_content_lang').on('click', function() {
			$('.kt-menu__item_lang').removeClass('d-none');
			// $(this).closest('.kt-header__topbar-item--langs').trigger('click');
			$(this).closest('.dropdown-menu').removeClass('show');
			localStorage.setItem("_manage_content_lang", '');
			showNotifyOnScreen({
					'notify_title': 'Done',
					'notify_msg': 'You can manage content in all languages now.',
					'notify_icon': 'icon la la-check-circle',
					'notify_type': 'success',
				});
		});

		$('.btn_manage_content_lang').on('click', function() {
			$('.kt-menu__item_lang').addClass('d-none');

			var lang = $(this).data('lang');
			$('.kt-menu__item-'+lang).removeClass('d-none');
			$('#img_active_flag').attr('src', '{{ asset("backend/media/icons/flags/flag_") }}'+lang+'.png');
			$('#img_active_flag').closest('span').trigger('click');
			localStorage.setItem("_manage_content_lang", lang);

			if (localStorage.getItem("_auto_press_this_btn") == 0) {
				showNotifyOnScreen({
						'notify_title': 'Done',
						'notify_msg': 'Only content in '+lang+' will be shown.',
						'notify_icon': 'icon la la-check-circle',
						'notify_type': 'success',
					});
			}
			localStorage.setItem("_auto_press_this_btn", 0);
		});

		if (localStorage.getItem("_first_time_load") == 0) {
			// alert(localStorage.getItem("_manage_content_lang"));
			if (localStorage.getItem("_manage_content_lang") != '') {
				var lang = localStorage.getItem("_manage_content_lang");
				localStorage.setItem("_auto_press_this_btn", 1);
				$('#btn_manage_content_lang_'+lang).trigger('click');
			}
		}
		else {
			// First init
			// alert('first time!!!');
			$('.kt-menu__item_lang').removeClass('d-none');
			$('.kt-header__topbar-item--langs > .dropdown-menu').removeClass('show');
			localStorage.setItem("_first_time_load", 0);
			localStorage.setItem("_manage_content_lang", '');
		}
		$('.kt-header__topbar-item--langs > .dropdown-menu').removeClass('show');
		
	});

	function copyUrl(t) 
	{
		$('#hd_txt_copy').val( $(t).data('url') );

		/* Get the text field */
		var copyText = document.getElementById("hd_txt_copy");

		/* Select the text field */
		copyText.select();
		copyText.setSelectionRange(0, 99999); /*For mobile devices*/

		/* Copy the text inside the text field */
		document.execCommand("copy");

		/* Alert the copied text */
		// alert("URL Copied: " + copyText.value);
			var resp = {};
		resp.notify_title = 'URL Copied'
		resp.notify_msg = 'You can Ctrl + V or paste anywhere.';
		resp.notify_icon = 'icon la la-copy';
		resp.notify_type = 'info';
		showNotifyOnScreen(resp);
	}

</script>
<!--end::Global Theme Bundle -->