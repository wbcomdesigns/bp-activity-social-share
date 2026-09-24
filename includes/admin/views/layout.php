<?php
/**
 * Admin page shell: branded header + sidebar nav + current tab.
 *
 * @package BPAS
 * @var array  $tabs    Tabs (label, render).
 * @var string $current Current tab key.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap bpas-admin">
	<header class="bpas-admin__header">
		<span class="bpas-admin__logo" aria-hidden="true"><?php echo BPAS\Icons::get( 'share-2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?></span>
		<div class="bpas-admin__heading">
			<h1 class="bpas-admin__title"><?php esc_html_e( 'Activity Share', 'bp-activity-social-share' ); ?></h1>
			<p class="bpas-admin__subtitle"><?php esc_html_e( 'Choose where members and visitors can share, and how the share menu looks.', 'bp-activity-social-share' ); ?></p>
		</div>
		<span class="bpas-admin__version"><?php echo esc_html( 'v' . ( defined( 'BPAS_PRO_VERSION' ) ? BPAS_PRO_VERSION . ' Pro' : BPAS_VERSION ) ); ?></span>
	</header>
	<hr class="wp-header-end" />

	<div class="bpas-admin__layout">
		<nav class="bpas-admin__nav" aria-label="<?php esc_attr_e( 'Activity Share settings', 'bp-activity-social-share' ); ?>">
			<ul>
				<?php foreach ( $tabs as $bpas_key => $bpas_tab ) : ?>
					<li>
						<?php
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
							<?php echo esc_html( $bpas_tab['label'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="bpas-admin__help">
				<a href="https://wordpress.org/plugins/bp-activity-social-share/#faq" target="_blank" rel="noopener"><?php esc_html_e( 'Documentation', 'bp-activity-social-share' ); ?></a>
				<a href="https://wordpress.org/support/plugin/bp-activity-social-share/" target="_blank" rel="noopener"><?php esc_html_e( 'Support', 'bp-activity-social-share' ); ?></a>
			</div>
		</nav>

		<main class="bpas-admin__main">
			<?php call_user_func( $tabs[ $current ]['render'] ); ?>
		</main>
	</div>

	<div class="bpas-admin__toast" role="status" aria-live="polite" hidden></div>
</div>
