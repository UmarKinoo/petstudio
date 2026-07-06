/**
 * Re-init UIkit when Elementor injects or updates Pet Studio widgets.
 */
( function ( $ ) {
	'use strict';

	var LOOP_FADE_SEC = 0.15;

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

	function isVideoHidden( video ) {
		var style = window.getComputedStyle( video );
		return style.display === 'none' || style.visibility === 'hidden';
	}

	function bindSeamlessVideoLoop( video ) {
		if ( video.classList.contains( 'ps-hero-video-b' ) ) {
			return;
		}

		var stackKey = 'psSeamless' + ( video.currentSrc || video.src || '' );
		if ( video.dataset.psSeamlessBound === stackKey ) {
			return;
		}

		if ( isVideoHidden( video ) ) {
			return;
		}

		video.dataset.psSeamlessBound = stackKey;
		video.classList.add( 'ps-hero-video-a' );
		video.muted = true;
		video.playsInline = true;
		video.setAttribute( 'playsinline', '' );

		var clone = video.cloneNode( true );
		clone.classList.add( 'ps-hero-video-b' );
		clone.classList.remove( 'ps-hero-video-a' );
		clone.removeAttribute( 'data-ps-seamless-bound' );
		clone.setAttribute( 'aria-hidden', 'true' );
		video.after( clone );

		function armCycle( leading, trailing ) {
			leading.classList.add( 'is-visible' );
			trailing.classList.remove( 'is-visible' );

			function onTimeUpdate() {
				if ( ! leading.duration || ! isFinite( leading.duration ) ) {
					return;
				}

				if ( leading.currentTime < leading.duration - LOOP_FADE_SEC ) {
					return;
				}

				leading.removeEventListener( 'timeupdate', onTimeUpdate );
				trailing.currentTime = 0;
				trailing.classList.add( 'is-visible' );
				leading.classList.remove( 'is-visible' );
				trailing.play().catch( function () {} );

				window.setTimeout( function () {
					leading.pause();
					armCycle( trailing, leading );
				}, LOOP_FADE_SEC * 1000 + 30 );
			}

			leading.addEventListener( 'timeupdate', onTimeUpdate );
		}

		clone.pause();
		clone.currentTime = 0;
		video.play().catch( function () {} );
		armCycle( video, clone );
	}

	function syncHeroVideos( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ps-hero-video-tile' ).forEach( function ( tile ) {
			tile.querySelectorAll( 'video' ).forEach( function ( video ) {
				if ( video.classList.contains( 'ps-hero-video-b' ) ) {
					return;
				}

				if ( isVideoHidden( video ) ) {
					video.pause();
					var partner = video.nextElementSibling;
					if ( partner && partner.classList.contains( 'ps-hero-video-b' ) ) {
						partner.pause();
					}
					return;
				}

				bindSeamlessVideoLoop( video );

				if ( video.paused ) {
					var start = function () {
						video.play().catch( function () {} );
					};
					if ( video.readyState >= 2 ) {
						start();
					} else {
						video.addEventListener( 'loadeddata', start, { once: true } );
					}
				}
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
