( function( $ ) {

	/////////////////////
	// Form processing //
	/////////////////////

	$( '#license-form' ).on( 'submit', function( event ) {

		event.preventDefault();
		wp.hooks.doAction( 'advanced-cron-manager.license.action', $(this) );

	} );

	/////////////
	// Actions //
	/////////////

	// license action
	wp.hooks.addAction( 'advanced-cron-manager.license.action', 'bracketspace/acm/license-action', function( $form ) {

		var $button = $form.find( '.button-secondary' ).first();

		var data = {
			'action'  : 'acm/license/' + $button.data( 'action' ),
			'license' : $form.find( '.license-key' ).val(),
			'nonce'   : $button.data( 'nonce' )
	    };

	    var button_label = $button.val();

	    $button.val( advanced_cron_manager_pro.i18n.saving );
	    $button.attr( 'disabled', true );

	    $.post( ajaxurl, data, function( response ) {

	        advanced_cron_manager.ajax_messages( response );

	        if ( response.success == true ) {

	        	if ( $button.data( 'action' ) == 'activate' ) {
	        		$form.parent().find( '.status' ).removeClass().addClass( 'status' ).text( advanced_cron_manager_pro.i18n.activated );
	        		$button.val( advanced_cron_manager_pro.i18n.activated );
	        	} else {
	        		$form.parent().find( '.status' ).removeClass().addClass( 'status' ).text( advanced_cron_manager_pro.i18n.deactivated );
	        		$button.val( advanced_cron_manager_pro.i18n.deactivated );
	        	}

	        } else {
	        	$button.val( button_label );
			    $button.attr( 'disabled', false );
	        }

	    } );

	} );

} )( jQuery );
