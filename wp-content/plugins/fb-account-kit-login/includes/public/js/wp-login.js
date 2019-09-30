(function($) {

    var body = $( 'body' ),
        loginForm = $( '#loginform' ),
        toggle = $( '.fb-ackit-toggle' ),
        fbakWrap = $( '.fb-ackit-wrap' ),
        fbakWrapOR = $( '.fb-ackit-or' );

    loginForm.append( fbakWrap );

    toggle.on( 'click', 'a', function(e) {
        e.preventDefault();
        if ( body.hasClass( 'jetpack-sso-repositioned' ) ) {
            fbakWrap.insertAfter( $('.jetpack-sso-clear') );
            fbakWrapOR.toggleClass('fb-ackit-or-toggle');
        }
        body.toggleClass( 'fb-ackit-form-display' );
    });

    // trigger to default
    body.toggleClass( 'fb-ackit-form-display' );

})(jQuery);