/**
 * Activity Share admin: saves through REST, keyboard reordering, live preview.
 */
( function () {
	'use strict';

	var config = window.bpasAdmin || {};
	var i18n = config.i18n || {};
	var toastEl = document.querySelector( '.bpas-admin__toast' );
	var toastTimer;

	function toast( message, isError ) {
		if ( ! toastEl ) {
			return;
		}
		toastEl.textContent = message;
		toastEl.classList.toggle( 'is-error', !! isError );
		toastEl.hidden = false;
		window.clearTimeout( toastTimer );
		toastTimer = window.setTimeout( function () {
			toastEl.hidden = true;
		}, 3000 );
	}

	// Only the fields present on this tab are sent (partial update).
	function collect( form ) {
		var data = {};
		var list = form.querySelector( '[data-bpas-networks]' );
		if ( list ) {
			data.networks = Array.prototype.map.call(
				list.querySelectorAll( 'input[name="networks[]"]:checked' ),
				function ( input ) {
					return input.value;
				}
			);
		}
		form.querySelectorAll( 'input[type="checkbox"]:not([name="networks[]"])' ).forEach( function ( input ) {
			data[ input.name ] = input.checked;
		} );
		form.querySelectorAll( 'input[type="text"]' ).forEach( function ( input ) {
			data[ input.name ] = input.value.trim();
		} );
		form.querySelectorAll( 'input[type="radio"]:checked' ).forEach( function ( input ) {
			data[ input.name ] = input.value;
		} );
		return data;
	}

	document.querySelectorAll( '[data-bpas-form]' ).forEach( function ( form ) {
		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			var button = form.querySelector( '[type="submit"]' );
			var label = button.textContent;
			button.disabled = true;
			button.textContent = i18n.saving || label;

			window.wp.apiFetch( { path: config.path, method: 'PATCH', data: collect( form ) } )
				.then( function () {
					toast( i18n.saved );
				} )
				.catch( function ( error ) {
					toast( ( error && error.message ) || i18n.failed, true );
				} )
				.finally( function () {
					button.disabled = false;
					button.textContent = label;
				} );
		} );
	} );

	// Keyboard-friendly reordering.
	document.addEventListener( 'click', function ( event ) {
		var move = event.target.closest( '[data-bpas-move]' );
		if ( ! move ) {
			return;
		}
		var row = move.closest( '.bpas-networks__row' );
		var up = 'up' === move.getAttribute( 'data-bpas-move' );
		var sibling = up ? row.previousElementSibling : row.nextElementSibling;
		if ( ! sibling ) {
			return;
		}
		row.parentNode.insertBefore( row, up ? sibling : sibling.nextElementSibling );
		move.focus();
		var position = Array.prototype.indexOf.call( row.parentNode.children, row ) + 1;
		var name = row.querySelector( '.bpas-networks__name' ).textContent;
		toast( ( i18n.moved || '%1$s %2$d' ).replace( '%1$s', name ).replace( '%2$d', position ) );
	} );

	// Live preview on the Appearance tab.
	var preview = document.querySelector( '[data-bpas-preview]' );
	if ( preview ) {
		document.addEventListener( 'input', function ( event ) {
			var input = event.target;
			if ( 'label' === input.name ) {
				var text = input.value.trim() || i18n.share || 'Share';
				preview.querySelectorAll( '.bpas-share__label, .bpas-share__sheet-title' ).forEach( function ( el ) {
					el.textContent = text;
				} );
			}
		} );
		document.addEventListener( 'change', function ( event ) {
			var input = event.target;
			var wrap = preview.querySelector( '.bpas-share-wrap' );
			var menu = preview.querySelector( '.bpas-share' );
			if ( ! wrap || ! menu ) {
				return;
			}
			if ( 'position' === input.name ) {
				wrap.classList.toggle( 'bpas-share-wrap--before', 'before' === input.value );
				wrap.classList.toggle( 'bpas-share-wrap--after', 'after' === input.value );
			}
			if ( 'colors' === input.name ) {
				menu.classList.toggle( 'bpas-share--brand', 'brand' === input.value );
				menu.classList.toggle( 'bpas-share--theme', 'theme' === input.value );
			}
		} );
	}

	// Dismissible Pro card (per browser).
	var card = document.querySelector( '[data-bpas-pro-card]' );
	if ( card ) {
		try {
			if ( window.localStorage.getItem( 'bpasProCardDismissed' ) ) {
				card.hidden = true;
			}
		} catch ( e ) {}
		card.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '[data-bpas-dismiss]' ) ) {
				card.hidden = true;
				try {
					window.localStorage.setItem( 'bpasProCardDismissed', '1' );
				} catch ( e ) {}
			}
		} );
	}
}() );
