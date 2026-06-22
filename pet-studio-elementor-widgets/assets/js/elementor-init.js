/**
 * Re-init UIkit when Elementor injects or updates Pet Studio widgets.
 */
( function ( $ ) {
	'use strict';

	function updateUikit( scope ) {
		if ( typeof UIkit === 'undefined' ) {
			return;
		}
		if ( typeof UIkitKojiro !== 'undefined' && UIkitKojiro.installed !== true ) {
			UIkit.use( UIkitKojiro );
		}
		UIkit.update( scope || document.body );
		syncHeroVideos( scope || document );
	}

	function syncHeroVideos( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ps-hero-video-tile' ).forEach( function ( tile ) {
			tile.querySelectorAll( 'video' ).forEach( function ( video ) {
				var style = window.getComputedStyle( video );
				var hidden = style.display === 'none' || style.visibility === 'hidden';

				if ( hidden ) {
					video.pause();
					return;
				}

				if ( video.readyState < 2 ) {
					video.load();
				}

				video.play().catch( function () {} );
			} );
		} );
	}

	var resizeTimer;
	$( window ).on( 'resize orientationchange', function () {
		clearTimeout( resizeTimer );
		resizeTimer = setTimeout( function () {
			syncHeroVideos( document );
		}, 150 );
	} );

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
