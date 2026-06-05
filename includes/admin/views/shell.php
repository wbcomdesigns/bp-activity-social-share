<?php
/**
 * Admin page shell: page header, sidebar nav, body slot.
 *
 * Receives from BPAS_Admin::render_page():
 *
 * @var array                  $bpas_tabs Tab registry keyed by slug.
 * @var string                 $active    Active tab slug.
 * @var string                 $page_url  Base URL (admin.php?page=buddypress-share).
 * @var Buddypress_Share_Admin $service   Retained legacy admin content renderer.
 *
 * Note: each settings tab body provides its OWN <form method="post"
 * action="options.php">, so the shell does NOT wrap the body in a form
 * or render a shared Save button — that would nest forms and break the
 * existing options.php + AJAX save paths.
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/includes/admin/views
 * @since      3.5.6
 */

defined( 'ABSPATH' ) || exit;

$bpas_version = defined( 'BP_SHARE_VERSION' ) ? BP_SHARE_VERSION : '';
?>
<div class="wrap bpas-admin">

	<header class="bpas-page-header">
		<div class="bpas-page-header__title">
			<span class="dashicons dashicons-share" aria-hidden="true"></span>
			<div>
				<h1><?php esc_html_e( 'BuddyPress Activity Social Share', 'buddypress-share' ); ?></h1>
				<p class="bpas-page-header__subtitle"><?php esc_html_e( 'Let members share BuddyPress activity updates to their social networks. Choose which networks appear and customize their colors.', 'buddypress-share' ); ?></p>
			</div>
		</div>
		<div class="bpas-page-header__actions">
			<?php if ( $bpas_version ) : ?>
				<span class="bpas-version-pill">v<?php echo esc_html( $bpas_version ); ?></span>
			<?php endif; ?>
		</div>
	</header>

	<?php
	/*
	 * Without this marker, core's common.js re-parents every .notice to
	 * sit right after the first <h1> it finds, which slots the "Settings
	 * saved" banner between our title and its subtitle.
	 */
	?>
	<hr class="wp-header-end">

	<div class="bpas-settings-layout">

		<aside class="bpas-settings-sidebar">
			<div class="bpas-settings-sidebar-brand">
				<span class="bpas-settings-brand-icon" aria-hidden="true">
					<span class="dashicons dashicons-share"></span>
				</span>
				<div class="bpas-settings-brand-text">
					<p class="bpas-settings-brand-name"><?php esc_html_e( 'Activity Social Share', 'buddypress-share' ); ?></p>
					<p class="bpas-settings-brand-sub"><?php esc_html_e( 'Plugin', 'buddypress-share' ); ?></p>
				</div>
			</div>
			<nav class="bpas-settings-sidebar-nav" aria-label="<?php esc_attr_e( 'Activity Social Share navigation', 'buddypress-share' ); ?>">
				<?php
				$bpas_printed_groups = array();
				$bpas_group_labels   = array(
					'settings' => esc_html__( 'Settings', 'buddypress-share' ),
				);
				foreach ( $bpas_tabs as $bpas_slug => $bpas_tab ) {
					$bpas_group = isset( $bpas_tab['group'] ) ? $bpas_tab['group'] : 'main';
					if ( 'main' !== $bpas_group && ! in_array( $bpas_group, $bpas_printed_groups, true ) ) {
						echo '<div class="bpas-snav-divider" role="separator"></div>';
						if ( isset( $bpas_group_labels[ $bpas_group ] ) ) {
							echo '<p class="bpas-snav-section-label">' . esc_html( $bpas_group_labels[ $bpas_group ] ) . '</p>';
						}
						$bpas_printed_groups[] = $bpas_group;
					}
					$bpas_classes  = 'bpas-snav-link';
					$bpas_classes .= $active === $bpas_slug ? ' bpas-snav-link--active' : '';
					echo '<a href="' . esc_url( $page_url . '&tab=' . $bpas_slug ) . '" class="' . esc_attr( $bpas_classes ) . '">';
					echo '<span class="dashicons ' . esc_attr( $bpas_tab['icon'] ) . '" aria-hidden="true"></span>';
					echo esc_html( $bpas_tab['label'] );
					echo '</a>';
				}
				?>

				<div class="bpas-snav-divider" role="separator"></div>
				<p class="bpas-snav-section-label"><?php esc_html_e( 'Resources', 'buddypress-share' ); ?></p>
				<a href="https://docs.wbcomdesigns.com/doc_category/buddypress-activity-social-share/" class="bpas-snav-link" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-book" aria-hidden="true"></span>
					<?php esc_html_e( 'Documentation', 'buddypress-share' ); ?>
					<span class="dashicons dashicons-external bpas-snav-link__ext" aria-hidden="true"></span>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'buddypress-share' ); ?></span>
				</a>
			</nav>
		</aside>

		<div class="bpas-settings-main">
			<?php
			// Render settings notices inside the content column so the banner
			// aligns with the panel chrome.
			settings_errors();

			// Each tab body provides its own form (when needed). The legacy
			// content renderers are reused unchanged.
			BPAS_Admin::render_tab_body( $active, $service );
			?>
		</div>

	</div>
</div>
