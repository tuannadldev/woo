( function( $ ) {

	wp.hooks.addAction( 'advanced-cron-manager.event.executed', 'bracketspace/acm/event-executed', function( event_hash, $event_row ) {

		var data = {
			'action': 'acm/logs/refresh',
			'event' : event_hash,
	    };

	    $.post( ajaxurl, data, function( response ) {

	        if ( response.success == true ) {

        		var $accordion = $event_row.find( '.details .content.logs .logs-accordion' );

        		if ( $accordion.length ) {
        			$accordion.replaceWith( response.data );
        		} else {
        			$event_row.find( '.details .content.logs' ).html( response.data );
        		}

	        }

	    } );

	} );

	wp.hooks.addAction( 'advanced-cron-manager.event.executed', 'bracketspace/acm/event-executed', function( event_hash, $event_row ) {

		var $logs_section = $( '#logs-section' )

		// check if general section is displayed
		if ( $logs_section.length == 0 ) {
			return false;
		}

		var data = {
			'action': 'acm/logs/refresh',
			'event' : null
	    };

	    $.post( ajaxurl, data, function( response ) {

	        if ( response.success == true ) {
	        	$logs_section.find( '.tile-content' ).html( response.data );
	        }

	    } );

	} );

} )( jQuery );
