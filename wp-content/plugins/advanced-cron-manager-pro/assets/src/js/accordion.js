( function( $ ) {

	$( '.tools_page_advanced-cron-manager' ).on( 'click', '.logs-accordion .toggle', function( event ) {

		event.preventDefault();

		var $this = $(this);

		if ( $this.next().hasClass( 'show' ) ) {

			$this.next().removeClass( 'show' );
			$this.next().hide();

		} else {

			$this.parent().parent().find( 'li .inner' ).removeClass( 'show' );
			$this.parent().parent().find( 'li .inner' ).hide();
			$this.next().toggleClass( 'show' );
			$this.next().toggle();

		}

	});

} )( jQuery );
