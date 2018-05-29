( function( $ ) {
	$( document ).ready( function() {
		$( '#tstmnls_gdpr' ).on( 'change', function() {
			if( $( this).is( ':checked' ) ) {
				$( '#tstmnls_gdpr_link_options' ).show();
			} else {
				$( '#tstmnls_gdpr_link_options' ).hide();
			}
		} ).trigger( 'change' );
	} );
} )( jQuery );
