/**
 * Re-init UIkit when Elementor injects or updates Pet Studio widgets.
 */
( function ( $ ) {
	'use strict';

	function updateUikit( scope ) {
		if ( typeof UIkit === 'undefined' ) {
			return;
		}
		UIkit.update( scope || document.body );
	}

	$( function () {
		updateUikit();
	} );

	$( window ).on( 'load', function () {
		updateUikit();
	} );

	if ( typeof elementorFrontend !== 'undefined' ) {
		$( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {
				updateUikit( $scope[ 0 ] );
			} );
		} );
	}
}( jQuery ) );
