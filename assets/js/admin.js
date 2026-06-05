/**
 * BuddyPress Activity Social Share: Admin chrome JS.
 *
 * Card-panel helpers: toast + promise-based confirm (skill Part 6
 * rules 10 + 12 — no native alert()/confirm()) plus the wp-color-picker
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

	var i18n = ( window.bpasAdmin && window.bpasAdmin.i18n ) || {};

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

	/* ─── Confirm modal (returns a Promise) ──────────────────── */

	function confirmModal( opts ) {
		opts = opts || {};
		return new Promise( function ( resolve ) {
			var backdrop = document.createElement( 'div' );
			backdrop.className = 'bpas-confirm-backdrop';

			var card = document.createElement( 'div' );
			card.className = 'bpas-confirm';
			card.setAttribute( 'role', 'dialog' );
			card.setAttribute( 'aria-modal', 'true' );

			var title = document.createElement( 'h2' );
			title.className = 'bpas-confirm__title';
			title.textContent = opts.title || '';
			if ( opts.title ) { card.appendChild( title ); }

			var desc = document.createElement( 'p' );
			desc.className = 'bpas-confirm__desc';
			desc.textContent = opts.message || '';
			if ( opts.message ) { card.appendChild( desc ); }

			var actions = document.createElement( 'div' );
			actions.className = 'bpas-confirm__actions';

			var cancelBtn = document.createElement( 'button' );
			cancelBtn.type = 'button';
			cancelBtn.className = 'bpas-btn bpas-btn-secondary';
			cancelBtn.textContent = opts.cancelLabel || i18n.confirmCancel || 'Cancel';

			var confirmBtn = document.createElement( 'button' );
			confirmBtn.type = 'button';
			confirmBtn.className = 'bpas-btn ' + ( 'danger' === opts.tone ? 'bpas-btn-danger' : 'bpas-btn-primary' );
			confirmBtn.textContent = opts.confirmLabel || i18n.confirmContinue || 'Continue';

			actions.appendChild( cancelBtn );
			actions.appendChild( confirmBtn );
			card.appendChild( actions );
			backdrop.appendChild( card );
			document.body.appendChild( backdrop );

			function cleanup( result ) {
				document.removeEventListener( 'keydown', onKey );
				if ( backdrop.parentNode ) {
					backdrop.parentNode.removeChild( backdrop );
				}
				resolve( result );
			}

			function onKey( e ) {
				if ( 'Escape' === e.key ) { cleanup( false ); }
				if ( 'Enter' === e.key ) { cleanup( true ); }
			}

			cancelBtn.addEventListener( 'click', function () { cleanup( false ); } );
			confirmBtn.addEventListener( 'click', function () { cleanup( true ); } );
			backdrop.addEventListener( 'click', function ( e ) {
				if ( e.target === backdrop ) { cleanup( false ); }
			} );
			document.addEventListener( 'keydown', onKey );
			confirmBtn.focus();
		} );
	}

	window.bpasConfirm = confirmModal;

	/* ─── Icon Colors tab: wp-color-picker ───────────────────── */

	$( function () {
		if ( $.fn.wpColorPicker ) {
			$( '.wb-social-icon-color-option input[type="text"]' ).wpColorPicker();
		}

		// Intercept destructive links/buttons that opt in via data-bpas-confirm.
		$( document ).on( 'click', '[data-bpas-confirm]', function ( e ) {
			var $el = $( this );
			if ( $el.attr( 'data-action' ) ) { return; }
			if ( $el.data( 'bpas-confirm-ok' ) ) { return; }
			e.preventDefault();
			var message = $el.data( 'bpas-confirm' ) || '';
			var tone    = $el.data( 'bpas-confirm-tone' ) || 'danger';
			confirmModal( { message: message, tone: tone } ).then( function ( ok ) {
				if ( ! ok ) { return; }
				$el.data( 'bpas-confirm-ok', true );
				if ( $el.is( 'a' ) ) {
					window.location.href = $el.attr( 'href' );
				} else if ( $el.is( 'button' ) || $el.is( 'input' ) ) {
					var form = $el.closest( 'form' ).get( 0 );
					if ( ! form ) { return; }
					var btnName  = $el.attr( 'name' ) || '';
					var btnValue = $el.attr( 'value' ) || '1';
					if ( btnName ) {
						var hidden   = document.createElement( 'input' );
						hidden.type  = 'hidden';
						hidden.name  = btnName;
						hidden.value = btnValue;
						form.appendChild( hidden );
					}
					form.submit();
				}
			} );
		} );
	} );

} )( jQuery );
