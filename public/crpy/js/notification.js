var current_page = 1;

function showNotify() {
    $('.notify-circle').hide();
    $('.notify').removeAttr('onclick');
    $('.notify').attr('onClick', 'hideNotify()');

    $('.notify-list').show();
    $('.notify-list').css('right', '0');
    $('.notify-list').css('transition', 'right 0.2s linear');
}

function hideNotify(notify_number) {
    // Set css topbar
    setNotifyNumber(notify_number);
    $('.notify').removeAttr('onclick');
    $('.notify').attr('onClick', 'showNotify()');

    // Set notify list
    $('body').find('.is-read').each(function(i, obj) {
        $(obj).removeClass('is-read');
        $(obj).find('.fas.fa-circle').addClass('d-none');
    });

    // Transition
    $('.notify-list').css('right', '-390px');
}

function setNotifyNumber(notify_number) {
    if(notify_number === undefined) {
        notify_number = 0;
    }
    $('.notify-number').html(numberFormat(notify_number));
    if(notify_number == 0) {
        $('.notify-circle').hide();
    }
    else {
        $('.notify-circle').show();
    }
}

function getCountNotification(url, user_id)
{
    $.ajax({
        type: 'json',
        method: 'GET',
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: { user_id:user_id },
        url: url + '/api/v1/notification/count',
        success: function(data) {
            if(data.status == "success") {
                setNotifyNumber(data.results);
            } else {
                console.log(data.status, data);
                return 0;
            }
        },
        error: function(data) {
            console.log("fail", data);
            return 0;
        }
    });
}

function setIsReadNotification(url, user_id, id) {
    $.ajax({
        type: 'json',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: { user_id:user_id, id:id },
        url: url + '/api/v1/notification/set-is-read',
        success: function(data) {
            if(data.status == "success") {
                setNotifyNumber(data.result);
            } else {
                console.log(data.status, data);
                return 0;
            }
        }
    });

    hideNotify(0);
}

function ajaxNotificationPagination(url, user_id, clear_html, more)
{
	if(current_page === null) {
		current_page = 1;
	} else if(current_page <= 0) {
		return;
	}

    if(more === true) {
        current_page++;
    }

	content_type = "#notify-list-body";

	$.ajax({
		url: url + '/api/v1/notification/list/pagination',
		type: 'json',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
			user_id: user_id,
            is_html: 1,
            page: current_page
		},
		beforeSend() {
			$('.notify-status').html('Loading...');
		},
        success: function(data) {
            if (data.status == 'success') {
                $('.notify-status').html('Load more, please click here');
                if(data.current_page >= data.total_page) {
                    $('.notify-status').hide();
                } else {
                    $('.notify-status').show();
                }
                if(clear_html) {
                    $(content_type).empty();
                }
                $(content_type).append(data.html);
                showNotify();
            }
        },
        fail: function(data, textStatus) {
            $(content_type).find('.loading').remove();
            $('.notify-status').html('Session expired. Please&nbsp;<a href="/login">log in</a>&nbsp;again.');
        }
	});
}
