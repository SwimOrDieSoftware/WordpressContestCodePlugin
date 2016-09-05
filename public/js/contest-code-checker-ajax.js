(function( $ ) {
	'use strict';

	$(document).ready(function($) {
		$("#contest_code_checker_submit").click(function( event ) {
			event.preventDefault();

			var data = {
				action: 'submit_contest_code',
				contestants_name: $("#contestants_name").val(),
				contestants_email: $("#contestants_email").val(),
				contestants_code: $("#contestants_code").val(),
				_wpnonce: $("#_wpnonce").val(),
			};

			// the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
	 		$.post(contest_code_data.ajaxurl, data, function(response) {
				var json = JSON.parse(response);

				$( "#ccc-dialog-message" ).html(json.message);
				$( "#ccc-dialog" ).dialog({
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
