<?php
/**
 * Admin page shell: page header + sidebar (brand, grouped tabs, resources) + current tab.
 *
 * @package BPAS
 * @var array  $tabs    Tabs (label, render, icon, group).
 * @var string $current Current tab key.
 */

defined( 'ABSPATH' ) || exit;

$bpas_pro       = defined( 'BPAS_PRO_VERSION' );
$bpas_docs      = 'https://docs.wbcomdesigns.com/bp-family/buddypress-activity-share/';
$bpas_resources = array(
	array( __( 'FAQ', 'bp-activity-social-share' ), 'dashicons-editor-help', $bpas_docs . 'faq/faq/' ),
	array( __( 'Documentation', 'bp-activity-social-share' ), 'dashicons-book', $bpas_docs ),
	array( __( 'Support', 'bp-activity-social-share' ), 'dashicons-sos', $bpas_pro ? 'https://wbcomdesigns.com/support/' : 'https://wordpress.org/support/plugin/bp-activity-social-share/' ),
);
$bpas_group     = null;
?>
<div class="wrap bpas-admin">
	<header class="bpas-admin__header">
		<div class="bpas-admin__heading">
			<span class="dashicons dashicons-share" aria-hidden="true"></span>
			<div>
				<h1 class="bpas-admin__title"><?php echo esc_html( $bpas_pro ? __( 'Activity Share Pro', 'bp-activity-social-share' ) : __( 'Activity Share', 'bp-activity-social-share' ) ); ?></h1>
				<p class="bpas-admin__subtitle"><?php esc_html_e( 'Choose where members and visitors can share, and how the share menu looks.', 'bp-activity-social-share' ); ?></p>
			</div>
		</div>
		<span class="bpas-admin__version"><?php echo esc_html( 'v' . ( $bpas_pro ? BPAS_PRO_VERSION : BPAS_VERSION ) ); ?></span>
	</header>
	<hr class="wp-header-end" />

	<div class="bpas-admin__layout">
		<aside class="bpas-admin__sidebar">
			<div class="bpas-admin__brand">
				<span class="bpas-admin__brand-icon" aria-hidden="true"><span class="dashicons dashicons-share"></span></span>
				<div>
					<p class="bpas-admin__brand-name"><?php esc_html_e( 'Activity Share', 'bp-activity-social-share' ); ?></p>
					<p class="bpas-admin__brand-sub"><?php echo esc_html( $bpas_pro ? _x( 'Pro', 'edition badge', 'bp-activity-social-share' ) : _x( 'Plugin', 'edition badge', 'bp-activity-social-share' ) ); ?></p>
				</div>
			</div>
			<nav class="bpas-admin__nav" aria-label="<?php esc_attr_e( 'Activity Share settings', 'bp-activity-social-share' ); ?>">
				<?php
				foreach ( $tabs as $bpas_key => $bpas_tab ) :
					$bpas_tab_group = $bpas_tab['group'] ?? __( 'Settings', 'bp-activity-social-share' );
					if ( $bpas_tab_group !== $bpas_group ) :
						if ( null !== $bpas_group ) :
							?>
							<div class="bpas-admin__nav-divider" role="separator"></div>
						<?php endif; ?>
						<p class="bpas-admin__nav-label"><?php echo esc_html( $bpas_tab_group ); ?></p>
						<?php
						$bpas_group = $bpas_tab_group;
					endif;
					$bpas_url = add_query_arg(
						array(
							'page' => BPAS\Admin\Admin::SLUG,
							'tab'  => $bpas_key,
						),
						admin_url( 'admin.php' )
					);
					?>
					<a class="bpas-admin__nav-link<?php echo $bpas_key === $current ? ' is-active' : ''; ?>"
						href="<?php echo esc_url( $bpas_url ); ?>"
						<?php echo $bpas_key === $current ? 'aria-current="page"' : ''; ?>>
						<span class="dashicons <?php echo esc_attr( $bpas_tab['icon'] ?? 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
						<?php echo esc_html( $bpas_tab['label'] ); ?>
					</a>
				<?php endforeach; ?>

				<div class="bpas-admin__nav-divider" role="separator"></div>
				<p class="bpas-admin__nav-label"><?php esc_html_e( 'Resources', 'bp-activity-social-share' ); ?></p>
				<?php foreach ( $bpas_resources as $bpas_link ) : ?>
					<a class="bpas-admin__nav-link" href="<?php echo esc_url( $bpas_link[2] ); ?>" target="_blank" rel="noopener">
						<span class="dashicons <?php echo esc_attr( $bpas_link[1] ); ?>" aria-hidden="true"></span>
						<?php echo esc_html( $bpas_link[0] ); ?>
						<span class="dashicons dashicons-external bpas-admin__nav-ext" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'bp-activity-social-share' ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		</aside>

		<main class="bpas-admin__main">
			<?php \BPAS\Admin\Admin::render_component_notice(); ?>
			<?php call_user_func( $tabs[ $current ]['render'] ); ?>
		</main>
	</div>

	<div class="bpas-admin__toast" role="status" aria-live="polite" hidden></div>
</div>
