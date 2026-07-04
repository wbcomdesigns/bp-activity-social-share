<?php
/**
 * Discover partial: ecosystem cross-promotion (read-only display view).
 *
 * Rendered inside shell.php via BPAS_Admin::render_tab_body(). This is a
 * pure presentation view — product cards that link out to other free
 * Wbcom Designs tools. No forms, no settings, no options, no AJAX.
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/includes/admin/views
 * @since      3.5.6
 */

defined( 'ABSPATH' ) || exit;

$bpas_ecosystem_img = BP_ACTIVITY_SHARE_PLUGIN_URL . 'assets/images/ecosystem/';

/*
 * Each product: brand mark (shipped in assets/images/ecosystem/), name,
 * a short admin-specific blurb (worded differently from readme.txt), and
 * the wbcomdesigns.com download URL.
 */
$bpas_ecosystem = array(
	array(
		'name' => __( 'BuddyNext', 'buddypress-share' ),
		'logo' => 'buddynext.svg',
		'desc' => __( 'A ready-made social network for WordPress with activity feeds, profiles, and member connections.', 'buddypress-share' ),
		'url'  => 'https://wbcomdesigns.com/downloads/buddynext/',
	),
	array(
		'name' => __( 'Jetonomy', 'buddypress-share' ),
		'logo' => 'jetonomy.svg',
		'desc' => __( 'Free forums, Q&A, and idea boards with trust-based moderation, tested past 100,000 topics.', 'buddypress-share' ),
		'url'  => 'https://wbcomdesigns.com/downloads/jetonomy/',
	),
	array(
		'name' => __( 'Mediaverse', 'buddypress-share' ),
		'logo' => 'mediaverse.svg',
		'desc' => __( 'Turn your community into a photo and video hub, with AI moderation keeping uploads clean.', 'buddypress-share' ),
		'url'  => 'https://wbcomdesigns.com/downloads/mediaverse/',
	),
	array(
		'name' => __( 'Listora', 'buddypress-share' ),
		'logo' => 'listora.svg',
		'desc' => __( 'Build searchable directories across ten listing types, from members to businesses to events.', 'buddypress-share' ),
		'url'  => 'https://wbcomdesigns.com/downloads/listora/',
	),
	array(
		'name' => __( 'Learnomy', 'buddypress-share' ),
		'logo' => 'learnomy.svg',
		'desc' => __( 'A free learning platform to build, publish, and sell online courses right on your site.', 'buddypress-share' ),
		'url'  => 'https://wbcomdesigns.com/downloads/learnomy/',
	),
);
?>

<div class="bpas-card">
	<div class="bpas-card__head">
		<p class="bpas-card__title"><?php esc_html_e( 'More Free Tools from Wbcom Designs', 'buddypress-share' ); ?></p>
		<p class="bpas-card__desc"><?php esc_html_e( 'Social sharing gets your members\' activity in front of new audiences. These free Wbcom Designs plugins give them more worth sharing: a full network, forums, media, directories, and courses.', 'buddypress-share' ); ?></p>
	</div>
	<div class="bpas-card__body">
		<div class="bpas-discover-grid">
			<?php foreach ( $bpas_ecosystem as $bpas_product ) : ?>
				<div class="bpas-discover-card">
					<span class="bpas-discover-card__logo" aria-hidden="true">
						<img src="<?php echo esc_url( $bpas_ecosystem_img . $bpas_product['logo'] ); ?>" alt="<?php echo esc_attr( $bpas_product['name'] ); ?>" width="52" height="52" loading="lazy" />
					</span>
					<h3 class="bpas-discover-card__title"><?php echo esc_html( $bpas_product['name'] ); ?></h3>
					<p class="bpas-discover-card__desc"><?php echo esc_html( $bpas_product['desc'] ); ?></p>
					<a class="bpas-btn bpas-btn-secondary bpas-discover-card__cta" href="<?php echo esc_url( $bpas_product['url'] ); ?>" target="_blank" rel="noopener">
						<?php esc_html_e( 'Get it free', 'buddypress-share' ); ?>
						<span class="dashicons dashicons-external" aria-hidden="true"></span>
						<span class="screen-reader-text">
							<?php
							/* translators: %s: product name. */
							echo esc_html( sprintf( __( '%s (opens in a new tab)', 'buddypress-share' ), $bpas_product['name'] ) );
							?>
						</span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
