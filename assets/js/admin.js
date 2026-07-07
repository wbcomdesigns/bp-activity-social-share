/**
 * BuddyPress Activity Social Share: Admin chrome JS.
 *
 * Card-panel helper: toast, plus the wp-color-picker
 * initialisation for the Icon Colors tab. The color-picker init was
 * moved here out of an inline <script> in admin/bp-icon-color-page.php
 * (gate F2 — no inline <script> in PHP).
 *
 * The General tab's drag/drop enable/disable lists + AJAX save are
 * handled by the retained content script admin/js/buddypress-share-admin.js,
 * unchanged by this migration.
 *
 * @since 3.5.6
 */
( function ( $ ) {
	'use strict';

	/* ─── Toast ─────────────────────────────────────────────── */

	function getToastHost() {
		var host = document.querySelector( '.bpas-toast-host' );
		if ( ! host ) {
			host = document.createElement( 'div' );
			host.className = 'bpas-toast-host';
			document.body.appendChild( host );
		}
		return host;
	}

	function toast( message, tone ) {
		tone = tone || 'info';
		var host = getToastHost();
		var el   = document.createElement( 'div' );
		el.className   = 'bpas-toast bpas-toast--' + tone;
		el.setAttribute( 'role', 'status' );
		el.textContent = String( message );
		host.appendChild( el );

		requestAnimationFrame( function () {
			el.classList.add( 'bpas-toast--visible' );
		} );

		window.setTimeout( function () {
			el.classList.remove( 'bpas-toast--visible' );
			window.setTimeout( function () {
				if ( el.parentNode ) {
					el.parentNode.removeChild( el );
				}
			}, 250 );
		}, 3600 );
	}

	window.bpasToast = toast;

	/* ─── Icon Colors tab: wp-color-picker ───────────────────── */

	$( function () {
		if ( $.fn.wpColorPicker ) {
			$( '.wb-social-icon-color-option input[type="text"]' ).wpColorPicker();
		}
	} );

} )( jQuery );
