<?php
/**
 * Welcome tab body — rendered inside the card-panel shell
 * (includes/admin/views/shell.php) by BPAS_Admin::render_tab_body().
 *
 * Uses the bpas-card / bpas-btn component vocabulary from
 * assets/css/admin.css so the tab matches the rest of the chrome.
 *
 * @link       http://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Share
 * @subpackage Buddypress_Share/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$bpas_welcome_resources = array(
	array(
		'icon'   => 'dashicons-book',
		'title'  => __( 'Documentation', 'buddypress-share' ),
		'text'   => __( 'We have prepared an extensive guide on BuddyPress Activity Social Share to learn all aspects of the plugin. You will find most of your answers here.', 'buddypress-share' ),
		'url'    => 'https://docs.wbcomdesigns.com/doc_category/buddypress-activity-social-share/',
		'button' => __( 'Read Documentation', 'buddypress-share' ),
	),
	array(
		'icon'   => 'dashicons-sos',
		'title'  => __( 'Support Center', 'buddypress-share' ),
		'text'   => __( 'We strive to offer the best customer care via our support center. Once your theme is activated, you can ask us for help anytime.', 'buddypress-share' ),
		'url'    => 'https://wbcomdesigns.com/support/',
		'button' => __( 'Get Support', 'buddypress-share' ),
	),
	array(
		'icon'   => 'dashicons-admin-comments',
		'title'  => __( 'Got Feedback?', 'buddypress-share' ),
		'text'   => __( 'We want to hear about your experience with the plugin. We would also love to hear any suggestions you may have for future updates.', 'buddypress-share' ),
		'url'    => 'https://wbcomdesigns.com/contact/',
		'button' => __( 'Send Feedback', 'buddypress-share' ),
	),
);
?>
<div class="bpas-card">
	<div class="bpas-card__head">
		<h2 class="bpas-card__title"><?php esc_html_e( 'About the Plugin', 'buddypress-share' ); ?></h2>
	</div>
	<div class="bpas-card__body">
		<p class="bpas-welcome-intro">
			<?php esc_html_e( 'BuddyPress Activity Social Share plugin allows users to share activity “Post Updates” to their social networking profiles.', 'buddypress-share' ); ?>
		</p>
		<p class="bpas-welcome-intro">
			<?php esc_html_e( 'This plugin adds the option ‘Share’ to BuddyPress Activity in post updates section parallel to the ‘Comment’, ‘Favorite’ and ‘Delete’ option.', 'buddypress-share' ); ?>
		</p>
	</div>
</div>

<div class="bpas-card">
	<div class="bpas-card__head">
		<h2 class="bpas-card__title"><?php esc_html_e( 'Help & Support Resources', 'buddypress-share' ); ?></h2>
		<p class="bpas-card__desc"><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'buddypress-share' ); ?></p>
	</div>
	<div class="bpas-card__body">
		<div class="bpas-welcome-grid">
			<?php foreach ( $bpas_welcome_resources as $bpas_resource ) : ?>
				<div class="bpas-welcome-resource">
					<span class="bpas-welcome-resource__icon" aria-hidden="true">
						<span class="dashicons <?php echo esc_attr( $bpas_resource['icon'] ); ?>"></span>
					</span>
					<h3 class="bpas-welcome-resource__title"><?php echo esc_html( $bpas_resource['title'] ); ?></h3>
					<p class="bpas-welcome-resource__text"><?php echo esc_html( $bpas_resource['text'] ); ?></p>
					<a href="<?php echo esc_url( $bpas_resource['url'] ); ?>" class="bpas-btn bpas-btn-primary" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $bpas_resource['button'] ); ?>
						<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'buddypress-share' ); ?></span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
