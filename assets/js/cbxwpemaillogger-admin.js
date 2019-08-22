(function( $ ) {
	'use strict';

	$(document).ready(function($) {
		$('.cbxwpemaillogger_actions_delete').on('click', function (e) {
			e.preventDefault();

			var $this = $(this);
			console.log($this);

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
	});

})( jQuery );
