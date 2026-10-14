/**
 * Google Analytics 4 and the cookie notice.
 *
 * - Analytics loads after the page has finished loading (it does not affect the LCP) and, when
 *   a domain is set (data-ga-host), only on that domain: staging and local copies are not
 *   measured.
 * - The notice is informative: it shows once, does not block the page and closes with its
 *   button. The choice is kept in the browser (localStorage), not in a cookie.
 */
( function () {
	'use strict';

	var root = document.querySelector( '[data-ci-privacy]' );
	if ( ! root ) {
		return;
	}
	var KEY = 'ci-cookie-notice';
	var id = root.getAttribute( 'data-ga-id' );
	var host = root.getAttribute( 'data-ga-host' );

	function loadAnalytics() {
		var current = window.location.hostname.replace( /^www\./, '' );
		if ( ! id || ( host && current !== host ) ) {
			return;
		}
		var script = document.createElement( 'script' );
		script.async = true;
		script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent( id );
		document.head.appendChild( script );
		window.dataLayer = window.dataLayer || [];
		window.gtag = function () {
			window.dataLayer.push( arguments );
		};
		window.gtag( 'js', new Date() );
		window.gtag( 'config', id );
	}

	function whenIdle( callback ) {
		if ( window.requestIdleCallback ) {
			window.requestIdleCallback( callback, { timeout: 3000 } );
		} else {
			window.setTimeout( callback, 1 );
		}
	}

	if ( document.readyState === 'complete' ) {
		whenIdle( loadAnalytics );
	} else {
		window.addEventListener( 'load', function () {
			whenIdle( loadAnalytics );
		} );
	}

	var notice = root.querySelector( '[data-ci-cookie-notice]' );
	if ( ! notice ) {
		return;
	}
	var seen = false;
	try {
		seen = window.localStorage.getItem( KEY ) === '1';
	} catch ( e ) {}
	if ( seen ) {
		return;
	}

	// After the page entrance, so it does not compete with the main content.
	window.setTimeout( function () {
		notice.hidden = false;
	}, 1500 );

	notice.querySelector( '[data-ci-cookie-close]' ).addEventListener( 'click', function () {
		try {
			window.localStorage.setItem( KEY, '1' );
		} catch ( e ) {}
		notice.hidden = true;
	} );
} )();
