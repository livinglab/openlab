jQuery(document).ready(function($){
        $('#signup_email').on('blur',function(e){
                var email = $(e.target).val().toLowerCase();
                var emailtype = '';
		var $emaillabel = $('label[for="signup_email"] div');
		var $validationdiv = $( '#validation-code' );
		var $emailconfirm = $( '#signup_email_confirm' );

		// Fade out and show a 'Checking' message.
		$emaillabel.fadeOut(function(){
			$emaillabel.html('&mdash; Checking...');
			$emaillabel.css('color', '#000');
			$emaillabel.fadeIn();
		});

                if ( 0 <= email.indexOf( 'mail.citytech.cuny.edu' ) ) {
                        emailtype = 'student';
                } else if ( 0 <= email.indexOf( 'citytech.cuny.edu' ) ) {
                        emailtype = 'fs';
                } else {
			emailtype = 'nonct';
		}

		if ( 'nonct' == emailtype ) {
			// Non-City Tech requires an AJAX request for verification.
			$.post( ajaxurl, {
				action: 'cac_ajax_email_check',
				'email': email
			},
			function(response) {
				var message = '';
				var show_validation = false;

				switch ( response ) {
					/*
					 * Return values:
					 *   1: success
					 *   2: no email provided
					 *   3: not a valid email address
					 *   4: unsafe
					 *   5: not in domain whitelist
					 *   6: email exists
					 *   7: Is a student email
					*/
					case "6" :
						message = 'An account already exists using that email address.';
						break;
					case "5" :
					case "4" :
						message = 'Must be a City Tech email address.';
						show_validation = true;
						break;
					case "3" :
						message = 'Not a well-formed email address. Please try again.';
						break;
					case "2" :
						message = 'The Email Address field is required.';
						break;

					case '1' :
					default :
						break;
				}

				if ( response != '1' && response != '5' && response != '4' ) {
					$emaillabel.fadeOut(function(){
						$emaillabel.html(message);
						$emaillabel.css('color', '#f00');
						$emaillabel.fadeIn();
					});
				} else if ( response == '1' ) {
					$emaillabel.fadeOut(function(){
						$emaillabel.html('&mdash; OK!');
						$emaillabel.css('color', '#000');
						$emaillabel.fadeIn();
					});
				} else {
					$emaillabel.fadeOut();

					// Don't add more than one
					if ( ! $validationdiv.length ) {
						var valbox = '<div id="validation-code" style="display:none"><label for="signup_validation_code">Signup code <em>(required)</em> <span style="color: #f00;">Required for non-City Tech addresses</span></label><input name="signup_validation_code" id="signup_validation_code" type="text" val="" /></div>';
						$('input#signup_email').after(valbox);
						$validationdiv = $( '#validation-code' );
					}
				}

				if ( show_validation ) {
					$validationdiv.show().find('input').focus();
				} else {
					$validationdiv.hide();
					$emailconfirm.focus();
				}

				set_account_type_fields();
			});

		} else {
			$validationdiv.hide();
			$emaillabel.hide();
			$emailconfirm.focus();
			set_account_type_fields();
		}

		function set_account_type_fields() {
			var newtypes = '';

			if ( 'student' == emailtype ) {
				newtypes += '<option value="Student">Student</option>';
				newtypes += '<option value="Alumni">Alumni</option>';
			}

			if ( 'fs' == emailtype ) {
				newtypes += '<option value="">----</option>';
				newtypes += '<option value="Faculty">Faculty</option>';
				newtypes += '<option value="Staff">Staff</option>';
			}

			if ( 'nonct' == emailtype ) {
				newtypes += '<option value="Non-City Tech">Non-City Tech</option>';
			}

			if ( '' == emailtype ) {
				newtypes += '<option value="">----</option>';
			}

			var $typedrop = $('#field_7');
			$typedrop.html(newtypes);

			/* 
			 * Because there is no alternative in the dropdown, the 'change' event never
			 * fires. So we trigger it manually.
			 */
			load_account_type_fields();
		}
        });

	$('input#signup_validation_code').live('blur',function(){
		var code = $(this).val();

		var vcodespan = $('label[for="signup_validation_code"] span');

		$(vcodespan).fadeOut(function(){
			$(vcodespan).html('&mdash; Checking...');
			$(vcodespan).css('color', '#000');
			$(vcodespan).fadeIn();
		});

		/* Handle email verification server side because there we have the functions for it */
		$.post( ajaxurl, {
			action: 'cac_ajax_vcode_check',
			'code': code
		},
		function(response){
			if ( '1' == response ) {
				$(vcodespan).fadeOut(function(){
					$(vcodespan).html('&mdash; OK!');
					$(vcodespan).css('color', '#000');
					$(vcodespan).fadeIn();
					$( 'div#submit' )
				});
			} else {
				$(vcodespan).fadeOut(function(){
					$(vcodespan).html('&mdash; Required for non-CUNY addresses');
					$(vcodespan).css('color', '#f00');
					$(vcodespan).fadeIn();
				});
			}
		});
	});

	var $account_type_field = $( '#field_' + OLReg.account_type_field );

	// Ensure that the account type field is set properly from the post
	$account_type_field.val( OLReg.post_data.field_7 );
	$account_type_field.children( 'option' ).each( function() {
		if ( OLReg.post_data.field_7 == $( this ).val() ) {
			$( this ).attr( 'selected', 'selected' );
		}
	} );

	$account_type_field.on( 'change', function() {
		load_account_type_fields();
	} );
	load_account_type_fields();

	//load register account type
	function load_account_type_fields() {
		var default_type = '';
		var selected_account_type = $account_type_field.val();

		if ( selected_account_type != "" ) {
			document.getElementById( 'signup_submit' ).style.display='';
		} else {
			document.getElementById( 'signup_submit' ).style.display='none';
		}

		$.ajax( ajaxurl, {
			data: {
				action: 'wds_load_account_type',
				account_type: selected_account_type,
				post_data: OLReg.post_data
			},
			method: 'POST',
			success: function( response ) {
				$( '#wds-account-type' ).html( response );
			}
		} );
	}
},(jQuery));
