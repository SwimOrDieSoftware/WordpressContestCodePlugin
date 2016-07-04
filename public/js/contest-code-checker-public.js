(function( $ ) {
	'use strict';
	
	$(document).ready(function($) {
		$("#contest_code_checker").validate({
		  rules: {
		    contestants_email: {
		      required: true,
		      email: true
		    }
		  }
		});
	});

})( jQuery );


