<?php
/**
 * Networks tab.
 *
 * @package BPAS
 * @var array $settings   Settings.
 * @var array $ordered    All networks, enabled first in the owner's order.
 * @var bool  $pro_active Pro installed and active.
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="bpas-admin__form" data-bpas-form>
	<section class="bpas-card">
		<header class="bpas-card__head">
			<h2 class="bpas-card__title"><?php esc_html_e( 'Networks', 'bp-activity-social-share' ); ?></h2>
			<p class="bpas-card__desc"><?php esc_html_e( 'Tick the networks to show in the share menu. Use the arrows to change their order.', 'bp-activity-social-share' ); ?></p>
		</header>
		<ol class="bpas-networks" data-bpas-networks>
			<?php foreach ( $ordered as $bpas_slug => $bpas_network ) : ?>
				<li class="bpas-networks__row" data-slug="<?php echo esc_attr( $bpas_slug ); ?>">
					<label class="bpas-networks__label">
						<input type="checkbox" name="networks[]" value="<?php echo esc_attr( $bpas_slug ); ?>" <?php checked( in_array( $bpas_slug, $settings['networks'], true ) ); ?> />
						<span class="bpas-networks__icon" style="--bpas-c: var(--bpas-brand-<?php echo esc_attr( sanitize_html_class( $bpas_slug ) ); ?>, currentColor)"><?php echo $bpas_network['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitised SVG. ?></span>
						<span class="bpas-networks__name"><?php echo esc_html( $bpas_network['label'] ); ?></span>
					</label>
					<span class="bpas-networks__move">
						<button type="button" class="bpas-icon-btn" data-bpas-move="up" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: network name. */ __( 'Move %s up', 'bp-activity-social-share' ), $bpas_network['label'] ) ); ?>"><?php echo BPAS\Icons::get( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?></button>
						<button type="button" class="bpas-icon-btn" data-bpas-move="down" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: network name. */ __( 'Move %s down', 'bp-activity-social-share' ), $bpas_network['label'] ) ); ?>"><?php echo BPAS\Icons::get( 'arrow-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?></button>
					</span>
				</li>
			<?php endforeach; ?>
		</ol>
		<p class="bpas-card__note"><?php esc_html_e( 'On phones, a "More options" button also opens the phone\'s own share menu (Instagram, Messages, and other apps).', 'bp-activity-social-share' ); ?></p>
	</section>

	<section class="bpas-card">
		<header class="bpas-card__head">
			<h2 class="bpas-card__title"><?php esc_html_e( 'Who and where', 'bp-activity-social-share' ); ?></h2>
		</header>
		<div class="bpas-fields">
			<label class="bpas-switch">
				<input type="checkbox" name="guest_share" value="1" <?php checked( $settings['guest_share'] ); ?> />
				<span class="bpas-switch__text">
					<strong><?php esc_html_e( 'Visitors can share', 'bp-activity-social-share' ); ?></strong>
					<span><?php esc_html_e( 'Show the share menu to people who are not logged in, on public posts only.', 'bp-activity-social-share' ); ?></span>
				</span>
			</label>
			<label class="bpas-switch">
				<input type="checkbox" name="show_on_profiles" value="1" <?php checked( $settings['show_on_profiles'] ); ?> />
				<span class="bpas-switch__text">
					<strong><?php esc_html_e( 'Share on member profiles', 'bp-activity-social-share' ); ?></strong>
					<span><?php esc_html_e( 'Adds Share next to the profile header buttons.', 'bp-activity-social-share' ); ?></span>
				</span>
			</label>
			<label class="bpas-switch">
				<input type="checkbox" name="show_on_groups" value="1" <?php checked( $settings['show_on_groups'] ); ?> />
				<span class="bpas-switch__text">
					<strong><?php esc_html_e( 'Share on public groups', 'bp-activity-social-share' ); ?></strong>
					<span><?php esc_html_e( 'Private and hidden groups never show outside sharing.', 'bp-activity-social-share' ); ?></span>
				</span>
			</label>
		</div>
	</section>

	<p class="bpas-admin__actions">
		<button type="submit" class="button button-primary"><?php esc_html_e( 'Save changes', 'bp-activity-social-share' ); ?></button>
	</p>
</form>

<?php if ( ! $pro_active ) : ?>
	<aside class="bpas-card bpas-card--pro" data-bpas-pro-card>
		<button type="button" class="bpas-icon-btn bpas-card__dismiss" data-bpas-dismiss aria-label="<?php esc_attr_e( 'Dismiss', 'bp-activity-social-share' ); ?>"><?php echo BPAS\Icons::get( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?></button>
		<h2 class="bpas-card__title"><?php esc_html_e( 'Want members to repost inside your community?', 'bp-activity-social-share' ); ?></h2>
		<p class="bpas-card__desc"><?php esc_html_e( 'Activity Share Pro adds reposting to profiles and groups, sending posts to friends, share buttons on blog posts and other content, and share analytics.', 'bp-activity-social-share' ); ?></p>
		<a class="button" href="https://wbcomdesigns.com/downloads/buddypress-activity-share-pro/" target="_blank" rel="noopener"><?php esc_html_e( 'See Activity Share Pro', 'bp-activity-social-share' ); ?></a>
	</aside>
<?php endif; ?>
