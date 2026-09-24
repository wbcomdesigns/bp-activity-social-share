<?php
/**
 * WB Plugins hub landing: every Wbcom plugin registered under the shared menu.
 *
 * @package BPAS
 */

defined( 'ABSPATH' ) || exit;

$bpas_entries = isset( $GLOBALS['submenu']['wbcomplugins'] ) && is_array( $GLOBALS['submenu']['wbcomplugins'] ) ? $GLOBALS['submenu']['wbcomplugins'] : array();
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- shared Wbcom hub hook.
$bpas_skip = (array) apply_filters( 'wbcom_hub_wrapper_helper_slugs', array( 'wbcom-plugins-page', 'wbcom-themes-page', 'wbcom-support-page', 'wbcom-license-page' ) );
?>
<div class="wrap bpas-admin">
	<header class="bpas-admin__header">
		<div class="bpas-admin__heading">
			<h1 class="bpas-admin__title"><?php esc_html_e( 'WB Plugins', 'bp-activity-social-share' ); ?></h1>
			<p class="bpas-admin__subtitle"><?php esc_html_e( 'Settings for your installed Wbcom Designs plugins.', 'bp-activity-social-share' ); ?></p>
		</div>
	</header>
	<hr class="wp-header-end" />
	<ul class="bpas-hub">
		<?php
		foreach ( $bpas_entries as $bpas_entry ) :
			$bpas_slug = (string) ( $bpas_entry[2] ?? '' );
			if ( '' === $bpas_slug || 'wbcomplugins' === $bpas_slug || in_array( $bpas_slug, $bpas_skip, true ) ) {
				continue;
			}
			?>
			<li class="bpas-card bpas-hub__card">
				<h2 class="bpas-card__title"><?php echo esc_html( wp_strip_all_tags( (string) $bpas_entry[0] ) ); ?></h2>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . rawurlencode( $bpas_slug ) ) ); ?>"><?php esc_html_e( 'Open settings', 'bp-activity-social-share' ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
