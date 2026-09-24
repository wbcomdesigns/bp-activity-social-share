/**
 * Activity Share - share menu behaviour.
 *
 * One set of document-level delegated listeners, so menus inside activity that
 * BuddyPress appends by AJAX work without re-initialising. The menu itself is a
 * native <details>, so it opens and closes even without this script.
 */
( function () {
	'use strict';

	var i18n = ( window.bpasShare && window.bpasShare.i18n ) || {};
	var phone = window.matchMedia( '(max-width: 640px)' );

	if ( navigator.share ) {
		document.documentElement.classList.add( 'bpas-can-native' );
	}

	function menuOf( el ) {
		return el && el.closest ? el.closest( 'details.bpas-share' ) : null;
	}

	function close( menu, focusToggle ) {
		if ( ! menu || ! menu.open ) {
			return;
		}
		menu.open = false;
		if ( focusToggle ) {
			var toggle = menu.querySelector( 'summary' );
			if ( toggle ) {
				toggle.focus();
			}
		}
	}

	function closeAll( except ) {
		document.querySelectorAll( 'details.bpas-share[open]' ).forEach( function ( menu ) {
			if ( menu !== except ) {
				close( menu, false );
			}
		} );
	}

	function announce( menu, message ) {
		var status = menu && menu.querySelector( '[data-bpas-status]' );
		if ( status ) {
			status.textContent = '';
			window.setTimeout( function () {
				status.textContent = message;
			}, 50 );
		}
	}

	function emit( menu, network ) {
		document.dispatchEvent( new CustomEvent( 'bpas:share', {
			detail: {
				network: network,
				type: menu.getAttribute( 'data-bpas-type' ),
				id: parseInt( menu.getAttribute( 'data-bpas-id' ), 10 ) || 0,
				url: menu.getAttribute( 'data-bpas-url' )
			}
		} ) );
	}

	function copy( text ) {
		if ( navigator.clipboard && window.isSecureContext ) {
			return navigator.clipboard.writeText( text );
		}
		return new Promise( function ( resolve, reject ) {
			var area = document.createElement( 'textarea' );
			area.value = text;
			area.setAttribute( 'readonly', '' );
			area.style.position = 'fixed';
			area.style.opacity = '0';
			document.body.appendChild( area );
			area.select();
			var ok = false;
			try {
				ok = document.execCommand( 'copy' );
			} catch ( e ) {
				ok = false;
			}
			document.body.removeChild( area );
			if ( ok ) {
				resolve();
			} else {
				reject();
			}
		} );
	}

	/*
	 * Themes clip action bars and reply lists (overflow: hidden, often as a float clearfix), which
	 * would cut the menu off. While it is open, unclip those ancestors - never scrolling ones - and
	 * keep their floats contained with flow-root so nothing moves. Restored on close.
	 */
	function unclip( menu ) {
		( menu.bpasUnclipped || [] ).forEach( function ( el ) {
			el.classList.remove( 'bpas-unclip', 'bpas-unclip--flow' );
		} );
		menu.bpasUnclipped = [];
		if ( ! menu.open ) {
			return;
		}
		var pageWidth = document.documentElement.clientWidth;
		for ( var el = menu.parentElement; el && el !== document.body; el = el.parentElement ) {
			var style = window.getComputedStyle( el );
			// Page-wide wrappers clip on purpose (to stop sideways scrolling); leave them alone.
			if ( el.offsetWidth >= pageWidth ) {
				break;
			}
			if ( /hidden|clip/.test( style.overflowX + style.overflowY ) ) {
				el.classList.add( 'bpas-unclip' );
				if ( 'block' === style.display ) {
					el.classList.add( 'bpas-unclip--flow' );
				}
				menu.bpasUnclipped.push( el );
			}
		}
	}

	// A dropdown that would open past the screen edge anchors to the other side instead.
	function keepOnScreen( menu ) {
		menu.classList.remove( 'bpas-share--flip' );
		var panel = menu.querySelector( '.bpas-share__panel' );
		if ( ! menu.open || ! panel || 'absolute' !== window.getComputedStyle( panel ).position ) {
			return; // Phones use the fixed bottom sheet.
		}
		var rect = panel.getBoundingClientRect();
		var rtl = 'rtl' === window.getComputedStyle( menu ).direction;
		if ( rtl ? rect.right > document.documentElement.clientWidth - 8 : rect.left < 8 ) {
			menu.classList.add( 'bpas-share--flip' );
		}
	}

	// Only one menu open at a time. `toggle` does not bubble, so capture it.
	document.addEventListener( 'toggle', function ( event ) {
		var menu = event.target;
		if ( ! menu.matches || ! menu.matches( 'details.bpas-share' ) ) {
			return;
		}
		if ( menu.open ) {
			closeAll( menu );
			// BuddyPress collapses long reply threads by adding .bp-hidden to every <li> under
			// .activity-comments - including our rows when the menu sits on a reply.
			// It hides them with the class and with jQuery .hide(); our rows never carry inline display.
			menu.querySelectorAll( '.bpas-share__item' ).forEach( function ( el ) {
				el.classList.remove( 'bp-hidden' );
				el.style.removeProperty( 'display' );
			} );
		}
		unclip( menu );
		keepOnScreen( menu );
	}, true );

	document.addEventListener( 'click', function ( event ) {
		var target = event.target;
		var menu = menuOf( target );

		if ( ! menu ) {
			closeAll( null );
			return;
		}

		if ( target.closest( '[data-bpas-close]' ) ) {
			event.preventDefault();
			close( menu, true );
			return;
		}

		// Click on the phone backdrop (the details element itself, outside the panel).
		if ( target === menu && menu.open ) {
			close( menu, true );
			return;
		}

		var copyBtn = target.closest( '[data-bpas-copy]' );
		if ( copyBtn ) {
			event.preventDefault();
			copy( menu.getAttribute( 'data-bpas-url' ) ).then( function () {
				announce( menu, i18n.copied );
				copyBtn.classList.add( 'is-copied' );
				window.setTimeout( function () {
					copyBtn.classList.remove( 'is-copied' );
				}, 2000 );
				emit( menu, 'copy' );
			}, function () {
				announce( menu, i18n.copyFailed || '' );
			} );
			return;
		}

		var nativeBtn = target.closest( '[data-bpas-native]' );
		if ( nativeBtn ) {
			event.preventDefault();
			navigator.share( {
				title: menu.getAttribute( 'data-bpas-title' ),
				url: menu.getAttribute( 'data-bpas-url' )
			} ).then( function () {
				emit( menu, 'native' );
				close( menu, false );
			}, function () {} );
			return;
		}

		var link = target.closest( 'a[data-bpas-network]' );
		if ( link ) {
			emit( menu, link.getAttribute( 'data-bpas-network' ) );
			// Desktop: small window keeps the member on the community. Phones: new tab / app.
			if ( link.hasAttribute( 'data-bpas-popup' ) && ! phone.matches ) {
				var win = window.open( link.href, 'bpas-share', 'width=600,height=600' );
				if ( win ) {
					win.opener = null;
					event.preventDefault();
				}
				// Blocked popup: the normal target="_blank" link opens instead.
			}
			close( menu, false );
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}
		var menu = menuOf( document.activeElement ) || document.querySelector( 'details.bpas-share[open]' );
		close( menu, true );
	} );
}() );
