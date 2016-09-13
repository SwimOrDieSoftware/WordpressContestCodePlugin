(function( $ ) {
	'use strict';

	$(document).ready(function($) {
		$("#contest_code_checker_submit").click(function( event ) {
			event.preventDefault();

			var data = {
				'action': 'submit_contest_code',
				'contestants_first_name': $("#contestants_name").val(),
				'contestants_last_name': $("#contestants_last_name").val(),
				'contestants_email': $("#contestants_email").val(),
				'contestants_code': $("#contestants_code").val(),
				'_wpnonce': $("#_wpnonce").val(),
			};

	 		jQuery.post(contest_code_data.ajaxurl, data , function(response) {
				var json = JSON.parse(response);
				var popup_width = 'auto';
				var popup_height = 'auto';

				if(contest_code_data.popup_width > 0) {
					popup_width = contest_code_data.popup_width;
				}

				if(contest_code_data.popup_height > 0) {
					popup_height = contest_code_data.popup_height;
				}

				$( "#ccc-dialog-message" ).html(json.message);
				$( "#ccc-dialog" ).dialog({
				  width: popup_width,
				  height: popup_height,
			      modal: true,
			      buttons: {
			        Ok: function() {
			          $( this ).dialog( "close" );
			        }
			      }
			    });
	 		});

	 		return false;
		});
	});

})( jQuery );
