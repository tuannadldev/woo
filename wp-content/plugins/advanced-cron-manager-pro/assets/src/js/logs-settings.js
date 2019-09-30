( function( $ ) {

	/////////////////////
	// Form processing //
	/////////////////////

	$( '#log-settings-form' ).on( 'submit', function( event ) {

		event.preventDefault();
		wp.hooks.doAction( 'advanced-cron-manager.logs.settings.action', $(this) );

	} );

	/////////////
	// Actions //
	/////////////

	// license action
	wp.hooks.addAction( 'advanced-cron-manager.logs.settings.action', 'bracketspace/acm/logs-settings-action', function( $form ) {

		var $button = $form.find( '.button-secondary' ).first();

		var data = {
			'action' : 'acm/logs/settings/save',
			'data'   : $form.serialize(),
			'nonce'  : $button.data( 'nonce' )
	    };

	    var button_label = $button.val();

	    $button.val( advanced_cron_manager_pro.i18n.saving );
	    $button.attr( 'disabled', true );

	    $.post( ajaxurl, data, function( response ) {

	        advanced_cron_manager.ajax_messages( response );

	        $button.val( button_label );
		    $button.attr( 'disabled', false );

	    } );

	} );

	/////////////
	// Helpers //
	/////////////

	$( '#log-settings-form' ).on( 'change', '.master-setting input', function() {

		if ( this.checked ) {
			$( this ).parent().nextAll( '.dependants' ).show();
		} else {
			$( this ).parent().nextAll( '.dependants' ).hide();
		}

	} );

} )( jQuery );
