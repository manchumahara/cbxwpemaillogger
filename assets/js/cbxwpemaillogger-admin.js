(function( $ ) {
	'use strict';

	$(document).ready(function($) {

		$('#cbxscratingreviewlogsearch-search-input').attr("placeholder", cbxwpemaillogger_dashboard.search_placeholder);

        //apply flatpickr javascript
        $("#cbxscratingreviewlog-logdate-input").flatpickr({
            disableMobile: "true",
            minDate   : new Date(),
            enableTime: false,
            dateFormat: 'Y-m-d',
            time_24hr    : true,
            defaultHour  : 0,
            defaultMinute: 0,
        });

		//delete email log
		$('.cbxwpemaillogger_actions_delete').on('click', function (e) {
			e.preventDefault();

			var $this = $(this);


			var $id 	= parseInt($this.data('id'));
			var $busy   = parseInt($this.data('busy'));

			if($busy == 0){
				Ply.dialog({
					"confirm-step": {
						ui: "confirm",
						data: {
							text: cbxwpemaillogger_dashboard.deleteconfirm,
							ok: cbxwpemaillogger_dashboard.deleteconfirmok, // button text
							cancel: cbxwpemaillogger_dashboard.deleteconfirmcancel
						},
						backEffect: "3d-flip[-180,180]"
					}
				}).always(function (ui) {
					if (ui.state) {
						// Ok
						//send ajax request to delete
						$this.data('busy', 1);

						$.ajax({

							type: "post",
							dataType: "json",
							url: cbxwpemaillogger_dashboard.ajaxurl,
							data: {
								action: "cbxwpemaillogger_log_delete",
								id: $id,
								security: cbxwpemaillogger_dashboard.nonce
							},
							success: function (data, textStatus, XMLHttpRequest) {
								Ply.dialog("alert", data.message);

								if(parseInt(data.success) == 1){
									$this.closest('tr.cbxwpemaillogger_row').remove();
								}

							}
						});

					} else {
						// Cancel
						// ui.by — 'overlay', 'x', 'esc'
					}
				});


			}
		});//end ajax log delete

		//re-send email
		$('.cbxwpemaillogger_actions_resend').on('click', function (e) {
			e.preventDefault();

			var $this = $(this);


			var $id 	= parseInt($this.data('id'));
			var $busy   = parseInt($this.data('busy'));

			if($busy == 0){
				Ply.dialog({
					"confirm-step": {
						ui: "confirm",
						data: {
							text: cbxwpemaillogger_dashboard.resendconfirm,
							ok: cbxwpemaillogger_dashboard.deleteconfirmok, // button text
							cancel: cbxwpemaillogger_dashboard.deleteconfirmcancel
						},
						backEffect: "3d-flip[-180,180]"
					}
				}).always(function (ui) {
					if (ui.state) {
// Ok
						//send ajax request to delete
						$this.data('busy', 1);

						$.ajax({

							type: "post",
							dataType: "json",
							url: cbxwpemaillogger_dashboard.ajaxurl,
							data: {
								action: "cbxwpemaillogger_log_resend",
								id: $id,
								security: cbxwpemaillogger_dashboard.nonce
							},
							success: function (data, textStatus, XMLHttpRequest) {
								$this.data('busy', 0);

								Ply.dialog("alert", data.message);
							}
						});
					}
				});
			}//end checking busy

		});//end ajax email resend
	});

})( jQuery );
