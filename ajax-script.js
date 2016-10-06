jQuery( document ).ready( function() {

	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  		return regex.test(email);
	}

        jQuery( '#intercom-submit' ).click( function( e ) {

		var fullname = jQuery('#fullname').val();
		var email = jQuery('#email').val();
		var message = jQuery('#message').val();
		var nonce = jQuery('#_wpnonce').val();

		if (!fullname || !email || !message || !isEmail(email)) {
			return;
		}
                e.preventDefault();

                var data = {
                        'action': 'intercom_contact_form',
                        'fullname': fullname,
			'email': email,
			'message': message,
			'nonce':nonce
                };
		
		jQuery('#intercom-form .ajax-loader').attr('class','ajax-loader').show();
		jQuery('#intercom-form .message').hide();

                jQuery.post(ajax_object.ajax_url, data, function(response) {

			jQuery('#intercom-form .ajax-loader').hide();
			
			var msg = '';

			if (response=='0') {
				msg = 'Thank you for your message. It has been sent.';
				jQuery('#intercom-form .message').attr('class','message success').hide().fadeIn('slow');
			} else {
				msg = 'Oops something went wrong.';
				jQuery('#intercom-form .message').attr('class','message error').hide().fadeIn('slow');
			}

			jQuery('#intercom-form .message').text(msg);
                });

        })
});

