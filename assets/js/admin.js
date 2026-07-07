/**
 * BuddyPress Activity Social Share: Admin chrome JS.
 *
 * wp-color-picker initialisation for the Icon Colors tab. Moved here out
 * of an inline <script> in admin/bp-icon-color-page.php (gate F2 — no
 * inline <script> in PHP).
 *
 * The General tab's drag/drop enable/disable lists + AJAX save are
 * handled by the retained content script admin/js/buddypress-share-admin.js,
 * unchanged by this migration.
 *
 * @since 3.5.6
 */
( function ( $ ) {
	'use strict';

	$( function () {
		if ( $.fn.wpColorPicker ) {
			$( '.wb-social-icon-color-option input[type="text"]' ).wpColorPicker();
		}
	} );

} )( jQuery );
