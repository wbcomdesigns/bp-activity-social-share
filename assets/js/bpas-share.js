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

	// The Clipboard API rejects when permission is denied (embedded frames, strict browsers),
	// so a rejection falls through to the textarea copy instead of failing outright.
	function copy( text ) {
		if ( navigator.clipboard && window.isSecureContext ) {
			return navigator.clipboard.writeText( text ).catch( function () {
				return legacyCopy( text );
			} );
		}
		return legacyCopy( text );
	}

	function legacyCopy( text ) {
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

	// Height of fixed / sticky bars stacked at the top of the screen (admin bar, sticky headers) at x.
	function coveredTop( menu, x ) {
		var top = 0;
		for ( var i = 0; i < 4; i++ ) {
			var bar = null;
			document.elementsFromPoint( x, top + 1 ).some( function ( el ) {
				for ( ; el && el !== document.documentElement; el = el.parentElement ) {
					if ( menu.contains( el ) || el.contains( menu ) ) {
						return false;
					}
					if ( /fixed|sticky/.test( window.getComputedStyle( el ).position ) ) {
						bar = el;
						return true;
					}
				}
				return false;
			} );
			var bottom = bar ? bar.getBoundingClientRect().bottom : 0;
			if ( bottom <= top ) {
				break;
			}
			top = bottom;
		}
		return top;
	}

	/*
	 * A dropdown that would open past either screen edge anchors to the other side instead.
	 * Too tall for the room below the button: it opens upward when there is more room above,
	 * and is capped to the side it opens on, so every row stays reachable by scrolling the menu.
	 */
	function keepOnScreen( menu ) {
		menu.classList.remove( 'bpas-share--flip', 'bpas-share--flip-end', 'bpas-share--up' );
		menu.style.removeProperty( '--bpas-panel-max' );
		var panel = menu.querySelector( '.bpas-share__panel' );
		if ( ! menu.open || ! panel || 'absolute' !== window.getComputedStyle( panel ).position ) {
			return; // Phones use the fixed bottom sheet.
		}
		var rect = panel.getBoundingClientRect();
		var toggle = menu.querySelector( 'summary' ).getBoundingClientRect();
		var height = document.documentElement.clientHeight;
		var below = height - toggle.bottom - 8;
		var above = toggle.top - coveredTop( menu, toggle.left + toggle.width / 2 ) - 8;
		if ( rect.height > below ) {
			var up = above > below;
			menu.classList.toggle( 'bpas-share--up', up );
			// Gap between button and panel (--bpas-space-1) is about 4px; keep it out of the cap.
			menu.style.setProperty( '--bpas-panel-max', Math.max( 120, ( up ? above : below ) - 4 ) + 'px' );
		}
		var width = document.documentElement.clientWidth;
		var rtl = 'rtl' === window.getComputedStyle( menu ).direction;
		var pastStart = rtl ? rect.right > width - 8 : rect.left < 8;
		var pastEnd = rtl ? rect.left < 8 : rect.right > width - 8;
		if ( pastStart ) {
			menu.classList.add( 'bpas-share--flip' );
		} else if ( pastEnd ) {
			menu.classList.add( 'bpas-share--flip-end' );
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
				// Sighted members see it too: the row itself reads "Link copied" for two seconds.
				var name = copyBtn.querySelector( '.bpas-share__name' );
				if ( name && undefined === copyBtn.bpasLabel ) {
					copyBtn.bpasLabel = name.textContent;
				}
				if ( name ) {
					name.textContent = i18n.copied;
				}
				copyBtn.classList.add( 'is-copied' );
				window.clearTimeout( copyBtn.bpasTimer );
				copyBtn.bpasTimer = window.setTimeout( function () {
					copyBtn.classList.remove( 'is-copied' );
					if ( name ) {
						name.textContent = copyBtn.bpasLabel;
					}
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

	/*
	 * In an activity action bar, Share takes the look of the theme's own buttons beside it
	 * (colour, size, weight, padding, border, background), written once as --bpas-bar-* on the
	 * root and read by .bpas-bar-matched rules. Measured again when the colour scheme changes.
	 * A bar with no neighbouring button keeps the plugin's own chip.
	 */
	var barProps = {
		color: 'color',
		'font-size': 'fontSize',
		'font-weight': 'fontWeight',
		'line-height': 'lineHeight',
		padding: 'padding',
		'border-width': 'borderTopWidth',
		'border-style': 'borderTopStyle',
		'border-color': 'borderTopColor',
		radius: 'borderTopLeftRadius',
		background: 'backgroundColor'
	};

	function matchBar() {
		var wrap = document.querySelector( '.bpas-share-wrap > .bpas-share--activity' );
		wrap = wrap && wrap.parentElement;
		if ( ! wrap || ! wrap.parentElement ) {
			return false;
		}
		var ref = null;
		Array.prototype.some.call( wrap.parentElement.children, function ( el ) {
			ref = el === wrap ? null : el.querySelector( 'a, button' );
			return !! ref;
		} );
		var root = document.documentElement;
		if ( ref ) {
			// Box from the button, text from the element that shows its label (themes style that span).
			var label = Array.prototype.find.call( ref.querySelectorAll( '*' ), function ( el ) {
				return ! el.children.length && el.textContent.trim() && el.getClientRects().length;
			} ) || ref;
			// Themes animate colours on a scheme switch: read the end values, not a mid-transition frame.
			var saved = [ ref, label ].map( function ( el ) {
				var was = el.style.getPropertyValue( 'transition' );
				el.style.setProperty( 'transition', 'none', 'important' );
				return was;
			} );
			var box = window.getComputedStyle( ref );
			var text = window.getComputedStyle( label );
			Object.keys( barProps ).forEach( function ( name ) {
				var from = /^(color|font-size|font-weight)$/.test( name ) ? text : box;
				root.style.setProperty( '--bpas-bar-' + name, from[ barProps[ name ] ] );
			} );
			root.style.setProperty( '--bpas-bar-height', ref.getBoundingClientRect().height + 'px' );
			// Neighbours showing only an icon (and maybe a count) at this width: Share does too.
			// Screen-reader-only words still exist in the DOM, so look for words that actually render.
			var showsWords = Array.prototype.some.call( ref.querySelectorAll( '*' ), function ( el ) {
				var rect = el.getBoundingClientRect();
				return ! el.children.length && /\p{L}/u.test( el.textContent ) && rect.width > 1 && rect.height > 1;
			} ) || Array.prototype.some.call( ref.childNodes, function ( node ) {
				return 3 === node.nodeType && /\p{L}/u.test( node.textContent );
			} );
			root.classList.toggle( 'bpas-bar-icons', ! showsWords );
			[ ref, label ].forEach( function ( el, i ) {
				el.style.setProperty( 'transition', saved[ i ] );
			} );
		}
		root.classList.toggle( 'bpas-bar-matched', !! ref );
		return true;
	}

	if ( ! matchBar() ) {
		// BuddyPress loads the stream by AJAX: match once the first activity arrives.
		var waitForBar = new window.MutationObserver( function () {
			if ( matchBar() ) {
				waitForBar.disconnect();
			}
		} );
		waitForBar.observe( document.body, { childList: true, subtree: true } );
	}
	// Themes switch the bar to icons-only on small screens.
	var resizeTimer;
	window.addEventListener( 'resize', function () {
		window.clearTimeout( resizeTimer );
		resizeTimer = window.setTimeout( matchBar, 150 );
	} );
	// Dark-mode toggles change the neighbours' colours.
	var schemeWatch = new window.MutationObserver( function () {
		window.requestAnimationFrame( matchBar );
	} );
	[ document.documentElement, document.body ].forEach( function ( el ) {
		schemeWatch.observe( el, { attributes: true, attributeFilter: [ 'class', 'data-theme', 'data-bx-mode' ] } );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}
		var menu = menuOf( document.activeElement ) || document.querySelector( 'details.bpas-share[open]' );
		close( menu, true );
	} );
}() );
