( function( $ ) {

	$( '.tools_page_advanced-cron-manager' ).on( 'click', '.load-more-logs', function( event ) {

		event.preventDefault();

		var $button    = $( this ),
			total      = $button.data( 'total' ),
			page       = $button.data( 'page' ),
			event_hash = $button.data( 'event' ),
			$container = $button.prev();

		$button.attr( 'disabled', true );
		var old_label = $button.text();
		$button.text( advanced_cron_manager_pro.i18n.loading );

		var data = {
			'action': 'acm/logs/load_more',
			'event' : event_hash,
			'page' : page + 1
	    };

	    $.post( ajaxurl, data, function( response ) {

	    	if ( response.success ) {
	    		$container.append( response.data );
	    	}

	    	if ( $container.find( '> li' ).length >= total ) {
	    		$button.remove();
	    	} else {
	    		$button.data( 'page', page + 1 ),
		        $button.attr( 'disabled', false );
				$button.text( old_label );
	    	}

	    } );

	} );

} )( jQuery );
