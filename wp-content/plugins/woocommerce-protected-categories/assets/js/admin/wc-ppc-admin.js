
(function( $, document, window, undefined ) {
	'use strict';
	/* PRODUCT CATEGORY VISIBILITY */

	var $catProtection = $( '#product_cat_protection' ),
		$catPasswords = $( '#product_cat_passwords' );

	var updateProdCatVisibility = function() {
		$catProtection.toggle( $( '#protected_visibility' ).prop( 'checked' ) );
	};

	$( '#product_cat_visibility' ).on( 'change', 'input:radio', function() {
		updateProdCatVisibility();
	} );

	$catProtection.on( 'change', 'input:checkbox', function() {
		var $protectionField = $( this ).parent( 'label' ).next( '.cat-protection__field' );
		$protectionField.toggle( $( this ).prop( 'checked' ) );
	} );

	$catPasswords.on( 'click', 'a.cat-password__icon', function() {
		var $passwordField = $( this ).parents( '.cat-password' );

		if ( 'add' === $( this ).data( 'action' ) ) {
			// Add password field
			var $passwordClone = $passwordField.clone(),
				index = $catPasswords.children( '.cat-password' ).last().data( 'index' ) + 1;

			if ( !$passwordClone.find( '.cat-password__icon--delete' ).length ) {
				$passwordClone.children( '.cat-password__icons' ).append( '<a class="cat-password__icon cat-password__icon--delete" data-action="delete" href="#"><span class="dashicons dashicons-minus"></span></a>' );
			}

			$passwordClone
				.attr( { 'data-first': false, 'data-index': index } )
				.find( '.cat-password__field' )
				.attr( { 'id': 'product_cat_password_' + index, 'value': '' } );

			$passwordClone.appendTo( $catPasswords );

		} else if ( 'delete' === $( this ).data( 'action' ) ) {
			// Remove password field
			var pass = $passwordField.find( 'input' ).val();

			// Remove if no password entered or if user confirms
			if ( !pass || (pass && confirm( wc_ppc_params.confirm_delete )) ) {
				$passwordField.remove();
			}
		}
		return false;
	} );

	$( document ).ready( function() {
		if ( $( '#addtag, #edittag' ).length ) {
			updateProdCatVisibility();

			if ( 'selectWoo' in $.fn ) {
				$( '#product_cat_user_roles, #product_cat_users' ).selectWoo();
			} else if ( 'select2' in $.fn ) {
				$( '#product_cat_user_roles, #product_cat_users' ).select2();
			}

			// Create an observer to monitor when a new category is added to table so we can reset Visibility options.
			if ( $( '#addtag' ).length && ('MutationObserver' in window) ) {
				// Select the node that will be observed for mutations
				var targetNode = document.getElementById( 'the-list' );

				// Options for the observer (which mutations to observe)
				var config = { childList: true };

				// Callback function to execute when mutations are observed
				var callback = function( mutationsList, observer ) {
					$.each( mutationsList, function( i, mutation ) {
						if ( mutation.type == 'childList' ) {
							// Reset visibility options
							var $catProtection = $( '#product_cat_protection' );
							$catProtection.find( '.cat-password__field, .cat-protection__select' ).val( null ).change();
							$catProtection.find( '.cat-password' ).not( ':eq(0)' ).remove();
							$catProtection.find( '.cat-protection__check' ).prop( 'checked', false ).change();
							$( '#public_visibility' ).prop( 'checked', true ).change();
						}
					} );
				};

				// Create an observer instance linked to the callback function
				var observer = new MutationObserver( callback );

				// Start observing the target node for configured mutations
				observer.observe( targetNode, config );
			}
		}
	} );

	/* SETTINGS PAGE */

	var toggleChildSettings = function( $parent ) {
		var show = false;
		var toggleVal = $parent.data( 'toggleVal' );
		var $children = $parent.closest( '.form-table' ).find( '.' + $parent.data( 'childClass' ) ).closest( 'tr' );

		if ( 'radio' === $parent.attr( 'type' ) ) {
			show = $parent.prop( 'checked' ) && toggleVal == $parent.val();
		} else if ( 'checkbox' === $parent.attr( 'type' ) ) {
			if ( typeof toggleVal === 'undefined' || 1 == toggleVal ) {
				show = $parent.prop( 'checked' );
			} else {
				show = !$parent.prop( 'checked' );
			}
		} else {
			show = (toggleVal == $parent.val());
		}

		$children.toggle( show );
	};

	var $formTables = $( '#mainform .form-table' );

	$formTables.on( 'change', '.toggle-parent', function() {
		toggleChildSettings( $( this ) );
	} );

	$( document ).ready( function() {
		$formTables.find( '.toggle-parent' ).each( function() {
			toggleChildSettings( $( this ) );
		} );
	} );

})( jQuery, document, window );
